# Member network: shared architecture contract

Six workstreams build this in parallel. Read this file first. Do not change anything in
"Owned by the lead". Everything else is split by workstream; stay inside your files.

## Product rules (the policy every workstream enforces)
- **Network area** = signed-in members with an **approved membership** (a tier) and a **verified email**.
  Enforced by middleware alias `member.approved` (+ `auth`). Service: `App\Services\MemberAccess`.
- **Listed in the directory** = network access + **identity verified** + profile visibility not `hidden`.
- **Send connection / collaboration requests** = `MemberAccess::canConnect()` (identity verified).
- **Messaging** = only between the two parties of an **accepted** connection.
- **Voting** = `MemberAccess::canVote($user, $motion->min_tier_rank)`; creating motions =
  `canCreateMotion()` (staff admins, or Sovereign Partners who are identity verified).
- Tiers (rank): sovereign-partner 3, principal-circle 2, corporate-affiliate 1 (`config/network.php`).
- Company verification adds a "Verified company" badge; it never replaces identity verification.
- No real biometric matching anywhere. Identity/company verification = secure document upload +
  **manual admin review** (the animated flow in `design/concepts/identity-verification-flow.html`
  is the UX to follow, as a simulated "checks" experience over a real review queue).

## Owned by the lead (do not edit)
`config/network.php`, all `database/migrations/2026_10_03_1000*`, `app/Models/*` listed below
(except where your workstream says you own it), `app/Models/User.php`, `app/Services/MemberAccess.php`,
`app/Http/Middleware/EnsureApprovedMember.php`, `bootstrap/app.php`, `routes/web.php`,
`resources/css/app.css`, `resources/views/components/account/shell.blade.php`,
`resources/views/components/admin/sidebar-nav.blade.php`, `deploy/*`, the membership card component and CSS.
The member sidebar and admin sidebar already list your pages **as soon as your route names exist**
(they use `Route::has`). So you never edit navigation: just register the route names below.

## Tables and models (already migrated in tests; models in `app/Models`)
`member_profiles` (MemberProfile), `identity_verifications` (IdentityVerification),
`company_verifications` (CompanyVerification), `connections` (Connection), `conversations`
(Conversation), `messages` (Message), `motions` (Motion), `votes` (Vote), `membership_plans`
(MembershipPlan), `plan_change_requests` (PlanChangeRequest). `User` has `profile()`,
`identityVerifications()`, `companyVerifications()`, `isIdentityVerified()`, `isCompanyVerified()`.
If your workstream needs an extra column, add a NEW migration named
`2026_10_03_2000NN_<what>.php` (NN = your workstream number x 10, e.g. profiles 10..19) that alters
the table; never edit the lead's migrations. You may add methods/relations to the model that your
workstream owns.

## Route files and route-name contract
Each workstream owns exactly one file and registers routes only there (`routes/<name>.php`,
already required by `routes/web.php`):

| # | Workstream | File | Member route names | Admin route names |
|---|---|---|---|---|
| 1 | Profiles | routes/profile.php | `account.profile` (GET /account/profile), `account.profile.update`, `account.profile.avatar` | - |
| 2 | Networking | routes/network.php | `network.index` (GET /network), `network.show` (GET /network/{slug}), `network.matches`, `network.connections` (GET /account/connections), `network.connect` (POST), `network.respond`, `network.withdraw` | `admin.network.index` |
| 3 | Messaging | routes/messaging.php | `messages.index` (GET /account/messages), `messages.show`, `messages.store`, `messages.poll` (JSON), `messages.report` | - |
| 4 | Voting | routes/voting.php | `votes.index` (GET /account/votes), `votes.show`, `votes.cast`, `votes.create`, `votes.store` | `admin.motions.index`, `admin.motions.*` |
| 5 | Verification | routes/verification.php | `verification.index` (GET /account/verification hub), `verification.identity.*`, `verification.company.*`, `verification.file` (secure download) | `admin.verifications.index`, `admin.verifications.*` |
| 6 | Plans + error pages | routes/plans.php | `plans.index` (GET /account/plans), `plans.request`, `plans.requests` | `admin.plans.*`, `admin.plan-requests.index`, `admin.plan-requests.*` |

Member routes live in `Route::middleware(['auth'])` (add `'member.approved'` where the policy says
so). Admin routes live in `Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin',
'two-factor.admin'])->group(...)` inside your own file. Route names must match the table (the
shared navigation depends on them).

## Shared services
- `App\Services\MemberAccess` (lead): `tierSlug/tierRank/isApprovedMember/isIdentityVerified/
  canUseNetwork/canBeListed/canConnect/canCreateMotion/canVote`.
- `App\Services\ProfileService` (workstream 1): `ensureFor(User): MemberProfile` (creates an empty
  profile with a unique slug), `completeness(MemberProfile): int` (0-100), `update(...)`.
- `App\Services\MatchService` (workstream 2): `suggestions(User, int $limit): Collection`.
- `App\Services\ConversationService` (stub by lead, extended by workstream 3): `ensureFor(Connection)`.
  Networking calls it when a request is accepted.
- `App\Services\VerificationService` (workstream 5): submit/approve/reject for identity and company.
  Workstream 2 and 4 only read verification via `User::isIdentityVerified()` / `isCompanyVerified()`.
- Blade component `<x-verified-badge :user="$user" />` (workstream 5) shows "Identity verified" /
  "Verified company" with Lucide icons (`x-icon`); workstream 1/2/3 may use it once it exists, and must
  tolerate its absence while building (wrap in `@if (View::exists('components.verified-badge'))`).
- Tier name lookup: `Application\Membership\Queries\ListMembershipTiers::bySlug($slug)->name`.
- Notifications: follow `app/Notifications/BrandedMessage.php` + `MembershipStatusNotification.php`
  (email via the branded layout, plus the `database` channel for dashboard items).

## Design system (match it exactly)
- Member pages use `<x-account.shell title="..." active="<key>">` and the `ac-*` classes in
  `resources/css/account.css` (panels `ac-panel`, `ac-stack`, buttons `ac-btn`/`ac-btn-solid`,
  inputs `ac-input`/`ac-field`/`ac-label`/`ac-err`, flashes `ac-flash`). Shell `active` keys:
  profile, network, messages, votes, verification, plans. New styles go ONLY in your own css file
  (`resources/css/<workstream>.css`, already imported). Admin pages use `<x-admin.shell>` and the
  existing admin components (look at an existing admin CRUD such as `resources/views/admin/team`).
- Palette (dark, gold accents) and rules: ink #0B0B0C, surface #17161A, raised #1F1E22, border #2A2825,
  gold #C9A25A, bright #E0BE7E, cream #F3EFE6, body #B9B4AC. Text on dark must be cream/body (>= 7:1).
  Never put gold text on gold, never muted-grey for real text, min 13px supporting text, 44px targets.
- Icons: Lucide only, via `<x-icon name="...">` (see `resources/views/components/icon.blade.php`; add a
  missing Lucide icon by copying its official body from
  `C:/Users/PC/AppData/Local/Temp/claude/C--laragon-www-underground/0fdf4c94-42e7-46bb-99e9-c720954e4fcc/scratchpad/lucide/node_modules/lucide-static/icons/<name>.svg`
  into that component's array. Only add what you need, append near the end of the array).
- Mobile first: every page works at 320px, no horizontal scroll; the member shell becomes a native-feel
  app on phones (bottom bar + sheet). Reduced-motion respected. Fonts/serif via the existing tokens.
- Seal: `<x-seal :size="64" />`. Membership cards: `<x-membership-card ...>` (see the plans workstream).

## Engineering rules
- Laravel 13, PHP 8.3, Blade, Tailwind v4. Follow existing patterns (controllers in `app/Http/Controllers`,
  form requests in `app/Http/Requests`, notifications in `app/Notifications`, services in `app/Services`).
- Authorisation on the server for every action (policies or explicit checks); CSRF on all forms;
  validate everything; rate-limit abuse-prone endpoints (`throttle:`); escape output.
- Uploaded files are PRIVATE (disk `local`, not `public`), served only through an authorised controller.
- Tests: PHPUnit feature tests for every route and rule you add. Run **only your own tests**:
  `php artisan test --filter=<YourTestClassOrNamespace>` (the lead runs the full suite at the end).
- Seeders/factories for demo data go in `database/seeders` / `database/factories` (do NOT call them from
  `DatabaseSeeder`; the lead decides). Never seed demo accounts into production flows.
- Pint: `vendor/bin/pint <your files>` before you finish.

## Parallel-work hygiene (IMPORTANT, six workers share this folder)
- Set a unique compiled-views folder in every shell you run tests/servers from:
  `export VIEW_COMPILED_PATH="C:/laragon/www/underground/storage/framework/views_w<N>"` (create it;
  N = your workstream number). This avoids Windows view-cache rename collisions.
- PHP: `export PATH="/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64:$PATH"`.
- Building CSS/JS: use ONLY `bash deploy/build-locked.sh` (it serializes builds). Do not run
  `npm run build` directly.
- Local preview server (optional, for screenshots): own sqlite file in your own scratch folder and a
  port of 8100 + N: `DB_CONNECTION=sqlite DB_DATABASE=<file> SESSION_DRIVER=file CACHE_STORE=file
  APP_ENV=local APP_URL=http://127.0.0.1:81NN MAIL_MAILER=log php artisan serve --port=81NN`; migrate it
  (`php artisan migrate --force`) and create throwaway users/data with a small script in your scratch folder.
  Stop the server and delete your scratch files when done.
- Do NOT touch the live site, do NOT deploy, do NOT run git commit/push/stash/checkout/reset.
  Do not edit another workstream's files; if you need something from them, use the contract above or
  report it in your final message.

## Final report format (short)
Files added/changed, route names registered, public service methods other workstreams can call, how
you verified (tests + visual), and anything unfinished or risky.
