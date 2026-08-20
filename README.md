# Underground Network Inc.

> Power beneath the surface.

The platform behind Underground Network Inc. — a global strategic advisory and
influence firm. Built as an **API-first, domain-driven** Laravel application on
MySQL: the JSON API is the product, and the marketing site is one of its clients.

**Status: work in progress.** The domain and application layers are largely in
place; persistence, the HTTP API surface and the landing page are not finished.
See [Roadmap](#roadmap).

---

## Architecture

The Laravel skeleton in `app/` stays deliberately thin. All business code lives
under `src/`, in four layers with a strict inward dependency rule — each layer
may only depend on the ones above it.

| Layer | Namespace | Path | Knows about |
| --- | --- | --- | --- |
| Domain | `Domain\` | `src/Domain` | Nothing. Pure PHP: entities, value objects, enums, domain events, repository *contracts*. |
| Application | `Application\` | `src/Application` | Domain only. Use cases: queries and actions, plus the DTOs they exchange. |
| Infrastructure | `Infrastructure\` | `src/Infrastructure` | Domain + Application. Eloquent records, repository implementations, service-provider bindings. |
| Interfaces | `Interfaces\` | `src/Interfaces` | Everything. HTTP controllers, form requests, API resources. |

The domain layer contains no Laravel imports at all — it is framework-agnostic
and testable without booting the container.

### Bounded contexts

- **Content** — the firm's catalogue and positioning: capabilities, sectors,
  metrics, engagement models, brand pillars, and the authored `Narrative`.
- **Insights** — published thinking from the think-tank practice.
- **Engagement** — the only write side. `ConfidentialInquiry` is an aggregate
  root with an explicit status machine (`received → under_review → engaged /
  declined → archived`) and its own opaque `InquiryReference` (`UG-2026-7KQ4XB`).

### API-first

`Application\Content\Queries\ComposeLandingPage` assembles a single
`LandingPage` DTO. Both `GET /api/v1/landing-page` and the server-rendered page
are projections of that one object, so the site and the API cannot drift.

---

## API surface

Base path `/api/v1`. Only the version handshake is live today; the rest is the
agreed contract.

| Method | Endpoint | Status |
| --- | --- | --- |
| `GET` | `/api/v1` | live |
| `GET` | `/api/v1/landing-page` | planned |
| `GET` | `/api/v1/capabilities`, `/capabilities/{slug}` | planned |
| `GET` | `/api/v1/sectors`, `/sectors/{slug}` | planned |
| `GET` | `/api/v1/metrics` | planned |
| `GET` | `/api/v1/engagement-models` | planned |
| `GET` | `/api/v1/pillars` | planned |
| `GET` | `/api/v1/insights`, `/insights/{slug}` | planned |
| `POST` | `/api/v1/inquiries` | planned |
| `GET` | `/api/v1/inquiries/{reference}` | planned |

---

## Getting started

Requires PHP 8.3+, Composer and MySQL 8.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create the schema, then serve:

```bash
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS underground CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

```bash
php artisan migrate --seed
```

```bash
php artisan serve
```

---

## Roadmap

- [x] Layered DDD skeleton, PSR-4 namespaces, container bindings
- [x] Content, Insights and Engagement domain models
- [x] Application use cases (`ComposeLandingPage`, `SubmitConfidentialInquiry`, …)
- [ ] Remaining Eloquent repositories (engagement models, pillars, insights, inquiries, narrative)
- [ ] Migrations, seeders and factories
- [ ] `Interfaces\Http\Api\V1` controllers, form requests and resources
- [ ] Landing page — desktop and mobile — against the reference designs in `design/`
- [ ] Feature and unit test suites

Reference designs for the landing page live in [`design/`](design/), alongside
the portrait used in the hero composition.

---

© 2026 Underground Network Inc. All rights reserved.
