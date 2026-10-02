<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MemberProfile;
use App\Models\User;
use Application\Membership\Queries\FindMembershipApplicationByEmail;
use Application\Membership\Queries\ListMembershipTiers;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Member profile lifecycle: creation with a safe slug and prefilled data,
 * updates, the weighted completeness score, avatar handling and the payload
 * that is safe to show to other members.
 */
final class ProfileService
{
    public const COMPLETE_THRESHOLD = 80;

    public const MAX_ROWS = 12;

    public function __construct(
        private readonly FindMembershipApplicationByEmail $findApplication,
        private readonly MemberAccess $access,
        private readonly ListMembershipTiers $tiers,
    ) {}

    /** The member's profile, created on first use and prefilled from their application. */
    public function ensureFor(User $user): MemberProfile
    {
        $existing = MemberProfile::query()->where('user_id', $user->id)->first();

        if ($existing !== null) {
            if ($existing->slug === null || $existing->slug === '') {
                $existing->forceFill(['slug' => $this->uniqueSlug((string) ($existing->display_name ?: $user->name))])->save();
            }

            return $existing;
        }

        $application = ($this->findApplication)($user->email);

        $profile = MemberProfile::query()->create([
            'user_id' => $user->id,
            'slug' => $this->uniqueSlug((string) $user->name),
            'display_name' => $user->name,
            'country' => $application?->country,
            'organisation_name' => $application?->organisation,
            'visibility' => 'members',
            'open_to_collaboration' => true,
        ]);

        return $this->refreshCompleteness($profile);
    }

    /** @param array<string, mixed> $data already validated */
    public function update(User $user, array $data): MemberProfile
    {
        $profile = $this->ensureFor($user);

        $allowed = [
            'display_name', 'headline', 'bio', 'city', 'country', 'timezone', 'languages', 'sectors',
            'supply_chain_roles', 'seeking_kinds', 'seeking_summary', 'offering_summary', 'services',
            'portfolio', 'website', 'linkedin_url', 'public_email', 'organisation_name',
            'organisation_role', 'organisation_size', 'organisation_website', 'visibility',
            'open_to_collaboration',
        ];

        $profile->fill(array_intersect_key($data, array_flip($allowed)))->save();

        return $this->refreshCompleteness($profile);
    }

    /**
     * The checklist behind the score: key, label, weight, done, and the
     * form section (anchor) where it is fixed.
     *
     * @return list<array{key: string, label: string, weight: int, done: bool, section: string}>
     */
    public function checklist(MemberProfile $p): array
    {
        $filled = static fn (mixed $v): bool => is_string($v) ? trim($v) !== '' : ! empty($v);
        $rows = static fn (mixed $v): int => is_array($v) ? count($v) : 0;

        return [
            ['key' => 'display_name', 'label' => 'Add your display name', 'weight' => 5, 'done' => $filled($p->display_name), 'section' => 'identity'],
            ['key' => 'headline', 'label' => 'Write a one-line headline', 'weight' => 6, 'done' => $filled($p->headline), 'section' => 'identity'],
            ['key' => 'bio', 'label' => 'Write a bio of at least 120 characters', 'weight' => 10, 'done' => mb_strlen(trim((string) $p->bio)) >= 120, 'section' => 'about'],
            ['key' => 'location', 'label' => 'Add your city and country', 'weight' => 6, 'done' => $filled($p->city) && $filled($p->country), 'section' => 'location'],
            ['key' => 'avatar', 'label' => 'Upload a profile photo', 'weight' => 5, 'done' => $filled($p->avatar_path), 'section' => 'identity'],
            ['key' => 'organisation_name', 'label' => 'Name your organisation', 'weight' => 8, 'done' => $filled($p->organisation_name), 'section' => 'organisation'],
            ['key' => 'organisation_role', 'label' => 'State your role there', 'weight' => 4, 'done' => $filled($p->organisation_role), 'section' => 'organisation'],
            ['key' => 'sectors', 'label' => 'Pick at least one sector', 'weight' => 8, 'done' => $rows($p->sectors) >= 1, 'section' => 'focus'],
            ['key' => 'supply_chain_roles', 'label' => 'Pick your supply-chain role', 'weight' => 7, 'done' => $rows($p->supply_chain_roles) >= 1, 'section' => 'focus'],
            ['key' => 'services', 'label' => 'List at least one service', 'weight' => 9, 'done' => $rows($p->services) >= 1, 'section' => 'services'],
            ['key' => 'portfolio', 'label' => 'Add a portfolio item', 'weight' => 8, 'done' => $rows($p->portfolio) >= 1, 'section' => 'portfolio'],
            ['key' => 'languages', 'label' => 'Add the languages you work in', 'weight' => 4, 'done' => $rows($p->languages) >= 1, 'section' => 'location'],
            ['key' => 'seeking_kinds', 'label' => 'Say what kind of partners you seek', 'weight' => 4, 'done' => $rows($p->seeking_kinds) >= 1, 'section' => 'seeking'],
            ['key' => 'seeking_summary', 'label' => 'Describe what you are looking for', 'weight' => 4, 'done' => $filled($p->seeking_summary), 'section' => 'seeking'],
            ['key' => 'offering_summary', 'label' => 'Describe what you offer', 'weight' => 6, 'done' => $filled($p->offering_summary), 'section' => 'seeking'],
            ['key' => 'links', 'label' => 'Add a website or LinkedIn link', 'weight' => 6, 'done' => $filled($p->website) || $filled($p->linkedin_url), 'section' => 'links'],
        ];
    }

    /** 0..100, weighted. */
    public function completeness(MemberProfile $profile): int
    {
        $score = 0;

        foreach ($this->checklist($profile) as $item) {
            if ($item['done']) {
                $score += $item['weight'];
            }
        }

        return min(100, $score);
    }

    /** Recompute and persist completeness and completed_at. */
    public function refreshCompleteness(MemberProfile $profile): MemberProfile
    {
        $score = $this->completeness($profile);

        $profile->forceFill([
            'completeness' => $score,
            'completed_at' => $score >= self::COMPLETE_THRESHOLD ? ($profile->completed_at ?? now()) : null,
        ])->save();

        return $profile;
    }

    /** Store a new 512px square avatar (public disk, profiles/) replacing any previous one. */
    public function storeAvatar(User $user, UploadedFile $file): MemberProfile
    {
        $profile = $this->ensureFor($user);
        $contents = (string) file_get_contents($file->getRealPath());
        $processed = $this->squareAvatar($contents);

        if ($processed !== null) {
            $path = 'profiles/'.Str::random(32).'.jpg';
            Storage::disk('public')->put($path, $processed);
        } else {
            $path = Storage::disk('public')->putFileAs('profiles', $file, Str::random(32).'.'.$file->extension());
        }

        $this->deleteAvatarFile($profile);
        $profile->forceFill(['avatar_path' => $path])->save();

        return $this->refreshCompleteness($profile);
    }

    public function removeAvatar(User $user): MemberProfile
    {
        $profile = $this->ensureFor($user);
        $this->deleteAvatarFile($profile);
        $profile->forceFill(['avatar_path' => null])->save();

        return $this->refreshCompleteness($profile);
    }

    /**
     * Only fields that are safe to show to other signed-in members. Never the
     * account email (only public_email when set), never a phone number, never
     * verification documents.
     *
     * @return array<string, mixed>
     */
    public function publicPayload(MemberProfile $profile): array
    {
        $profile->loadMissing('user');
        $user = $profile->user;
        $name = $profile->display_name ?: (string) $user?->name;
        $tierSlug = $user !== null ? $this->access->tierSlug($user) : null;

        $labels = static fn (?array $keys, array $map): array => collect($keys ?? [])->map(fn ($k) => $map[$k] ?? null)->filter()->values()->all();

        return [
            'slug' => $profile->slug,
            'name' => $name,
            'initials' => $this->initials($name),
            'headline' => $profile->headline,
            'bio' => $profile->bio,
            'avatar_url' => $profile->avatarUrl(),
            'city' => $profile->city,
            'country' => $profile->country,
            'languages' => $profile->languages ?? [],
            'sectors' => $profile->sectors ?? [],
            'sector_labels' => $labels($profile->sectors, (array) config('network.sectors')),
            'supply_chain_roles' => $profile->supply_chain_roles ?? [],
            'supply_chain_role_labels' => $labels($profile->supply_chain_roles, (array) config('network.supply_chain_roles')),
            'seeking_kinds' => $profile->seeking_kinds ?? [],
            'seeking_kind_labels' => $labels($profile->seeking_kinds, (array) config('network.seeking_kinds')),
            'seeking_summary' => $profile->seeking_summary,
            'offering_summary' => $profile->offering_summary,
            'services' => $profile->services ?? [],
            'portfolio' => $profile->portfolio ?? [],
            'website' => $profile->website,
            'linkedin_url' => $profile->linkedin_url,
            'public_email' => $profile->public_email,
            'organisation' => [
                'name' => $profile->organisation_name,
                'role' => $profile->organisation_role,
                'size' => $profile->organisation_size,
                'website' => $profile->organisation_website,
            ],
            'open_to_collaboration' => (bool) $profile->open_to_collaboration,
            'tier_slug' => $tierSlug,
            'tier_name' => $tierSlug !== null ? $this->tiers->bySlug($tierSlug)?->name : null,
            'identity_verified' => $user?->isIdentityVerified() ?? false,
            'company_verified' => $user?->isCompanyVerified() ?? false,
            'completeness' => (int) $profile->completeness,
        ];
    }

    public function initials(string $name): string
    {
        $letters = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');

        return $letters !== '' ? $letters : 'U';
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug(Str::limit($name, 60, '')) ?: 'member';
        $slug = $base;
        $i = 2;

        while (MemberProfile::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function deleteAvatarFile(MemberProfile $profile): void
    {
        if ($profile->avatar_path !== null && str_starts_with($profile->avatar_path, 'profiles/')) {
            Storage::disk('public')->delete($profile->avatar_path);
        }
    }

    /** Centre-crop to a square and resize to 512px as JPEG (null when GD is unavailable or the image is unreadable). */
    private function squareAvatar(string $contents): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $src = @imagecreatefromstring($contents);

        if ($src === false) {
            return null;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $side = min($w, $h);
        $dst = imagecreatetruecolor(512, 512);
        imagefill($dst, 0, 0, (int) imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, intdiv($w - $side, 2), intdiv($h - $side, 2), 512, 512, $side, $side);

        ob_start();
        imagejpeg($dst, null, 86);
        $out = (string) ob_get_clean();

        return $out !== '' ? $out : null;
    }
}
