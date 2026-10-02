<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\MemberProfile;
use App\Models\User;
use App\Services\ProfileService;
use Application\Membership\Actions\ApplyForMembership;
use Application\Membership\DataTransferObjects\MembershipApplicationPayload;
use Database\Seeders\MembershipTierSeeder;
use Domain\Shared\ValueObjects\EmailAddress;
use Domain\Shared\ValueObjects\Slug;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function service(): ProfileService
    {
        return $this->app->make(ProfileService::class);
    }

    private function save(User $user, string $section, array $data)
    {
        return $this->actingAs($user)->from(route('account.profile'))
            ->put(route('account.profile.update'), ['section' => $section] + $data);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('account.profile'))->assertRedirect(route('login'));
        $this->put(route('account.profile.update'), [])->assertRedirect(route('login'));
        $this->post(route('account.profile.avatar'), [])->assertRedirect(route('login'));
    }

    public function test_ensure_for_is_idempotent_and_slugs_are_unique_and_safe(): void
    {
        $a = User::factory()->create(['name' => 'Amara Diallo', 'email' => 'amara@example.com']);
        $b = User::factory()->create(['name' => 'Amara Diallo', 'email' => 'amara2@example.com']);

        $first = $this->service()->ensureFor($a);
        $again = $this->service()->ensureFor($a);
        $second = $this->service()->ensureFor($b);

        $this->assertTrue($first->is($again));
        $this->assertSame(1, MemberProfile::query()->where('user_id', $a->id)->count());
        $this->assertSame('amara-diallo', $first->slug);
        $this->assertSame('amara-diallo-2', $second->slug);
        $this->assertStringNotContainsString('example', (string) $first->slug);
        $this->assertStringNotContainsString((string) $a->id, ltrim((string) $first->slug, 'a-z-'));
    }

    public function test_first_creation_prefills_from_the_membership_application(): void
    {
        $this->seed(MembershipTierSeeder::class);
        $user = User::factory()->create(['name' => 'Isabelle Fontaine', 'email' => 'isa@example.com']);

        $this->app->make(ApplyForMembership::class)(new MembershipApplicationPayload(
            tier: Slug::fromString('principal-circle'),
            name: 'Isabelle Fontaine',
            organisation: 'Fontaine Capital',
            email: EmailAddress::fromString('isa@example.com'),
            phone: null,
            country: 'France',
            statement: 'Requesting consideration for a principal-level advisory relationship with Underground.',
        ));

        $profile = $this->service()->ensureFor($user);

        $this->assertSame('Isabelle Fontaine', $profile->display_name);
        $this->assertSame('France', $profile->country);
        $this->assertSame('Fontaine Capital', $profile->organisation_name);
    }

    public function test_completeness_math_and_completed_at(): void
    {
        $user = User::factory()->create();
        $profile = $this->service()->ensureFor($user);

        $this->assertSame(5, $this->service()->completeness($profile));
        $this->assertNull($profile->completed_at);

        $full = MemberProfile::factory()->complete()->create();
        $this->assertSame(100, $this->service()->completeness($full));

        $short = MemberProfile::factory()->complete()->create(['bio' => 'Too short']);
        $this->assertSame(90, $this->service()->completeness($short));

        $this->service()->refreshCompleteness($full);
        $this->assertNotNull($full->fresh()->completed_at);
        $this->assertSame(100, $full->fresh()->completeness);
    }

    public function test_page_renders_for_a_user_without_a_membership_with_a_notice(): void
    {
        $user = User::factory()->create(['name' => 'No Member']);

        $this->actingAs($user)->get(route('account.profile'))
            ->assertOk()
            ->assertSee('My Profile')
            ->assertSee('approved membership')
            ->assertSee('identity verification')
            ->assertSee('Preview as others see me');

        $this->assertDatabaseHas('member_profiles', ['user_id' => $user->id]);
    }

    public function test_sections_save_and_a_member_can_only_change_their_own_profile(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->service()->ensureFor($b);
        $bBefore = MemberProfile::query()->where('user_id', $b->id)->first()->display_name;

        $this->save($a, 'identity', ['display_name' => 'Alpha Person', 'headline' => 'Builder'])
            ->assertRedirect(route('account.profile').'#identity');

        $this->assertSame('Alpha Person', MemberProfile::query()->where('user_id', $a->id)->first()->display_name);
        $this->assertSame($bBefore, MemberProfile::query()->where('user_id', $b->id)->first()->display_name);

        // A smuggled user_id / foreign field is ignored.
        $this->save($a, 'about', ['bio' => 'Hello', 'user_id' => $b->id, 'slug' => 'hacked'])->assertSessionHasNoErrors();
        $this->assertSame('Hello', MemberProfile::query()->where('user_id', $a->id)->first()->bio);
        $this->assertNotSame('hacked', MemberProfile::query()->where('user_id', $a->id)->first()->slug);
    }

    public function test_validation_rules(): void
    {
        $user = User::factory()->create();

        $this->save($user, 'about', ['bio' => str_repeat('x', 2001)])->assertSessionHasErrors('bio');
        $this->save($user, 'focus', ['sectors' => ['not-a-sector']])->assertSessionHasErrors('sectors.0');
        $this->save($user, 'focus', ['supply_chain_roles' => ['wizard']])->assertSessionHasErrors('supply_chain_roles.0');
        $this->save($user, 'links', ['website' => 'javascript:alert(1)'])->assertSessionHasErrors('website');
        $this->save($user, 'links', ['linkedin_url' => 'https://example.com/me'])->assertSessionHasErrors('linkedin_url');
        $this->save($user, 'visibility', ['visibility' => 'public'])->assertSessionHasErrors('visibility');
        $this->save($user, 'nonsense', [])->assertSessionHasErrors('section');
    }

    public function test_services_and_portfolio_row_limits_and_blank_rows_are_dropped(): void
    {
        $user = User::factory()->create();
        $row = fn (int $i) => ['title' => 'Service '.$i, 'description' => 'd', 'sector' => 'technology-innovation'];

        $thirteen = array_map($row, range(1, 13));
        $this->save($user, 'services', ['services' => $thirteen])->assertSessionHasErrors('services');

        $this->save($user, 'services', ['services' => [['title' => '', 'description' => '', 'sector' => '']]])
            ->assertSessionHasErrors('services');

        $this->save($user, 'services', ['services' => [$row(1), ['title' => '', 'description' => '', 'sector' => ''], $row(2)]])
            ->assertSessionHasNoErrors();
        $this->assertCount(2, MemberProfile::query()->where('user_id', $user->id)->first()->services);

        $this->save($user, 'portfolio', ['portfolio' => array_map(fn ($i) => ['title' => 'P'.$i], range(1, 13))])
            ->assertSessionHasErrors('portfolio');
        $this->save($user, 'portfolio', ['portfolio' => [['title' => 'Solar farm', 'year' => '2023', 'link' => 'https://example.com/p']]])
            ->assertSessionHasNoErrors();
        $this->assertSame('Solar farm', MemberProfile::query()->where('user_id', $user->id)->first()->portfolio[0]['title']);
    }

    public function test_empty_chip_selection_clears_the_list_and_languages_split(): void
    {
        $user = User::factory()->create();
        $this->save($user, 'focus', ['sectors' => ['finance-investments'], 'supply_chain_roles' => ['financier']]);
        $this->save($user, 'focus', []);
        $profile = MemberProfile::query()->where('user_id', $user->id)->first();
        $this->assertSame([], $profile->sectors);

        $this->save($user, 'location', ['city' => 'Lagos', 'country' => 'Nigeria', 'languages' => 'English, French , ']);
        $this->assertSame(['English', 'French'], $profile->fresh()->languages);
    }

    public function test_avatar_upload_validation_and_square_resize(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('account.profile.avatar'), ['avatar' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')])
            ->assertSessionHasErrors('avatar');
        $this->actingAs($user)->post(route('account.profile.avatar'), ['avatar' => UploadedFile::fake()->image('big.jpg')->size(4000)])
            ->assertSessionHasErrors('avatar');

        $this->actingAs($user)->post(route('account.profile.avatar'), ['avatar' => UploadedFile::fake()->image('me.png', 800, 600)])
            ->assertSessionHasNoErrors();

        $profile = MemberProfile::query()->where('user_id', $user->id)->first();
        $this->assertStringStartsWith('profiles/', (string) $profile->avatar_path);
        Storage::disk('public')->assertExists($profile->avatar_path);

        if (extension_loaded('gd')) {
            $size = getimagesizefromstring(Storage::disk('public')->get($profile->avatar_path));
            $this->assertSame([512, 512], [$size[0], $size[1]]);
        }

        $old = $profile->avatar_path;
        $this->actingAs($user)->delete(route('account.profile.avatar.destroy'))->assertRedirect();
        $this->assertNull($profile->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($old);
    }

    public function test_public_payload_never_leaks_private_data_and_card_renders(): void
    {
        $user = User::factory()->create(['email' => 'secret-login@example.com']);
        $profile = MemberProfile::factory()->complete()->create(['user_id' => $user->id, 'public_email' => null]);

        $payload = $this->service()->publicPayload($profile);
        $this->assertStringNotContainsString('secret-login@example.com', json_encode($payload));
        $this->assertNull($payload['public_email']);
        $this->assertArrayNotHasKey('email', $payload);
        $this->assertArrayNotHasKey('phone', $payload);
        $this->assertArrayNotHasKey('user_id', $payload);
        $this->assertSame(['Energy & Natural Resources'], $payload['sector_labels']);

        $profile->update(['public_email' => 'hello@example.com']);
        $this->assertSame('hello@example.com', $this->service()->publicPayload($profile->fresh())['public_email']);

        $html = view('components.profile-card', ['profile' => $profile->load('user')])->render();
        $this->assertStringContainsString($profile->display_name, $html);
        $this->assertStringNotContainsString('secret-login@example.com', $html);
    }
}
