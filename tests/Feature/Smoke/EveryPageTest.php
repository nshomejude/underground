<?php

declare(strict_types=1);

namespace Tests\Feature\Smoke;

use App\Models\CompanyVerification;
use App\Models\Connection;
use App\Models\IdentityVerification;
use App\Models\MemberProfile;
use App\Models\Motion;
use App\Models\PlanChangeRequest;
use App\Models\User;
use App\Services\ConversationService;
use Database\Seeders\MembershipPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Feature\Messaging\MessagingTestCase;

/** Opens every GET page as guest, verified member and admin; none may fail with a server error. */
final class EveryPageTest extends MessagingTestCase
{
    use RefreshDatabase;

    public function test_no_page_returns_a_server_error(): void
    {
        $this->seed(MembershipPlanSeeder::class);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $a = $this->member('Alice Adeyemi');
        $b = $this->member('Bruno Bello', 'principal-circle');
        $admin = User::factory()->create(['is_admin' => true]);
        foreach ([$a, $b] as $u) {
            $u->forceFill(['email_verified_at' => now()])->save();
            MemberProfile::factory()->complete()->create(['user_id' => $u->id]);
            IdentityVerification::factory()->approved()->create(['user_id' => $u->id]);
        }
        CompanyVerification::factory()->inReview()->create(['user_id' => $a->id]);
        $conn = Connection::factory()->between($a, $b)->accepted()->create();
        $conv = app(ConversationService::class)->ensureFor($conn);
        $motion = Motion::factory()->create();
        $req = PlanChangeRequest::factory()->create(['user_id' => $a->id]);
        $idv = IdentityVerification::query()->first();
        $cv = CompanyVerification::query()->first();

        $values = [
            'slug' => $b->profile->slug, 'connection' => $conn->id, 'conversation' => $conv->id, 'motion' => $motion->id,
            'planChangeRequest' => $req->id, 'identityVerification' => $idv->id, 'companyVerification' => $cv->id,
            'verification' => $idv->id, 'user' => $a->id, 'plan' => $req->to_plan_id, 'membershipPlan' => $req->to_plan_id,
        ];

        $bad = []; $this->withoutExceptionHandling();
        foreach ([null, $a, $admin] as $actor) {
            if ($actor) { $this->actingAs($actor); } else { auth()->logout(); }
            foreach (Route::getRoutes() as $route) {
                if (! in_array('GET', $route->methods(), true)) { continue; }
                $uri = $route->uri();
                if (preg_match('#^(admin/(applications|inquiries)/1$|_|storage|up$|sanctum|api/|build)#', $uri)) { continue; }
                $url = preg_replace_callback('#\{(\w+)\??\}#', fn ($m) => (string) ($values[$m[1]] ?? 1), $uri);
                try {
                    file_put_contents("C:/laragon/www/crawl.log", $url."
", FILE_APPEND); $res = $this->get('/'.ltrim($url, '/'));
                    if (method_exists($res, "status") && $res->status() >= 500) { $bad[] = ($actor?->name ?? 'guest').' '.$url.' '.$res->status(); }
                } catch (\Throwable $e) {
                    if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || $e instanceof \Illuminate\Auth\AuthenticationException || $e instanceof \Illuminate\Auth\Access\AuthorizationException || $e instanceof \Illuminate\Validation\ValidationException) { continue; }
                    $bad[] = ($actor?->name ?? 'guest').' '.$url.' '.get_class($e).': '.substr($e->getMessage(), 0, 200);
                }
            }
        }

        $this->assertSame([], $bad, implode("\n", $bad));
    }
}
