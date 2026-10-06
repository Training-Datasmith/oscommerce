# osCommerce Online Merchant — full test suite plan

Planning document for the **Training-Datasmith** corpus fork (`ai-training-20260418`). Goal: a **host- and CI-runnable** test story with **unit** coverage of core logic and **LAMP integration** coverage of Shop/Admin flows, recorded in `configuration/test-baselines.tsv` when green.

**Matrix today:** `runnable=no`, no `composer.json`, no PHPUnit in the standard datasmith sweep path.

---

## 1. Objectives

| Objective | Definition of done |
|-----------|-------------------|
| **Unit suite** | PHPUnit runs on Linux and Windows **without** MySQL; **0 failures, 0 errors**; skips only where explicitly documented (OS/platform). |
| **Integration suite** | Shop installed via **Setup** on **Linux** (cloud agent canonical); tests use **Apache** where routing/`.htaccess`/real HTTP matter, and **plain PHP** (CLI or in-process bootstrap) where they do not; **0F/0E** on the integration group. |
| **Coverage** | **100% line coverage** of testable PHP under `osCommerce/OM/` as the long-term target (phased; see §5–§7). External live I/O (real payment gateways) stays stubbed or faked, but code paths are exercised. |
| **Corpus integration** | `phpunit.xml.dist` at repo root; matrix notes document the runner; **dev-only** `composer.json` when dependencies are actually used (PHPUnit, optional HTTP client). |
| **PHP 8.4** | Bootstrap and tests pass on **PHP 8.4** (training branch); fix strict-type and deprecation issues as encountered. |

Non-goals for v1: full browser E2E (Playwright), **live** payment gateway calls, performance/load testing. (Admin CRUD and modules are in scope over time via unit + integration tests toward full coverage.)

### 1.1 Dependency management (Composer)

- **Production** continues to use the OM autoloader; no Composer autoload in the storefront runtime.
- Add **`composer.json` only as dev tooling** when it pulls in something we use: at minimum **PHPUnit** for a pinned version on PHP 8.4; add **`symfony/http-client`** (or similar) only if integration tests adopt it instead of curl.
- Do **not** add Composer “by default” for matrix optics alone—if the suite ever ran from a PHPUnit PHAR with no other deps, that would be acceptable; today pinning PHPUnit via Composer is the practical choice.
- `vendor/` in `.gitignore`; host sweep and cloud agent run `composer install` when `composer.json` exists.

---

## 2. Current state (inventory)

### 2.1 Application shape

- **Entry:** `index.php` → `OSCOM::initialize()` → site `Controller` (Setup / Shop / Admin).
- **Config:** `osCommerce/OM/Config/settings.ini` (+ optional `local_settings.ini`); default site in repo is **Setup** (installer).
- **Code layout:** `osCommerce/OM/Core/` (~1.2k PHP files), sites under `Core/Site/{Setup,Shop,Admin}/`.
- **Public assets:** `public/` (JS/CSS); storefront/admin UIs are server-rendered PHP templates.
- **Database:** MySQL scripts in `osCommerce/OM/Core/Site/Setup/sql/` (`oscommerce.sql`, `oscommerce_innodb.sql`, `oscommerce_sample_data.sql`).

### 2.2 Existing tests

| Asset | Notes |
|-------|--------|
| `osCommerce/OM/Tests/configuration.xml` | Legacy PHPUnit config (no `phpunit.xml` at repo root). |
| `osCommerce/OM/Tests/bootstrap.php` | Registers test autoloader, calls `OSCOM::initialize()`. |
| `osCommerce/OM/Tests/osCommerce/OM/Core/HTMLTest.php` | ~20 methods; extends `\PHPUnit_Framework_TestCase` (PHPUnit 3/4 API). |
| `osCommerce/OM/Tests/osCommerce/OM/Core/ErrorHandler.php` | Test stub overriding production `ErrorHandler` (no-op `initialize()`). |

**Gaps:** No modern PHPUnit, no root `phpunit.xml.dist`, no CI runner, no integration tests, no sandbox profile. HTML tests assume full `OSCOM::initialize()` (config + site bootstrap), not isolated unit bootstraps.

---

## 3. Target architecture

Two suites, one PHPUnit toolchain (10+), three execution contexts: **unit (any OS)**, **integration via PHP CLI**, **integration via Apache**.

```text
                    ┌──────────────────────────────────────┐
                    │  phpunit.xml.dist (+ dev composer)   │
                    └──────────────────┬───────────────────┘
                                       │
          ┌────────────────────────────┼────────────────────────────┐
          ▼                            ▼                            ▼
   tests/unit/                  tests/integration/           Cloud Linux agent
   (no DB, host OK)             @group integration          (canonical for LAMP)
          │                            │
          │                     ┌──────┴──────┐
          │                     ▼             ▼
          │              PHP CLI /           Apache HTTP
          │              in-process          (htaccess, cookies,
          │              bootstrap           multi-site URLs)
          ▼
   OM/Core + Site logic
   toward 100% coverage
```

### 3.1 Unit tests (`tests/unit`)

**Scope:** All **deterministic** logic under `osCommerce/OM/`—Core utilities first, then Site modules, models (with doubles), and admin/shop actions that can run without Apache.

**Bootstrap order (phase 1 examples):**

- `HTML` (port existing `HTMLTest`; fix brittle date/timezone expectations).
- `Hash` / `Hash\Salt`, `DateTime`, `Access`, `CreditCard` helpers, `Upload` sanitization, then breadth across Core and Site classes.

**Bootstrap (`tests/bootstrap-unit.php`):**

- Load OM autoloader only; **do not** call full `OSCOM::initialize()` unless a test explicitly needs it.
- Inject minimal `settings.ini` fixtures or in-memory config for tests that need keys.
- Keep test-only `ErrorHandler` stub on the include path (existing Autoloader custom-path behavior).

**PHPUnit config:**

- `testsuite name="unit"` → `tests/unit`
- Enable **coverage** reporting (`phpunit.xml` `<source>` / Xdebug or PCOV on Linux); track progress toward **100%** of `osCommerce/OM`.
- `executionOrder="random"` after static-registry `tearDown` resets (Slim-style).

### 3.2 Integration tests (`tests/integration`) — LAMP

**Canonical environment:** **Linux cloud agent** (`remote-test-*` branch, `remote-phpunit-cloud-grind` skill)—Apache + PHP 8.4 + MySQL. Windows host runs **unit** only; integration baselines come from cloud.

| Layer | Component | Role |
|-------|-----------|------|
| **L** | Apache 2.4 | Required for tests that depend on **real HTTP**, `public/.htaccess`, vhost `HTTP_HOST`, or multi-step Setup/Shop URLs. |
| **A** | mod_php or php-fpm | **PHP 8.4** with `ext-pdo_mysql`, `ext-json`, `ext-mbstring`, `ext-xml`, `ext-gd` (catalog/images as coverage expands). |
| **M** | MySQL 8.x | Empty DB per run; install via **Setup** (not hand-imported production config as the default path). |
| **P** | osCommerce | **Setup wizard** completes install; load **full** `oscommerce_sample_data.sql` as part of install (per Setup flow / documented harness step). |

**Install (decided): drive Setup**

- Automate the **Setup** site install (HTTP and/or internal harness that invokes the same steps the wizard uses)—schema, configuration, admin account, sample data.
- Treat “SQL dump + hand-written `local_settings.ini`” as an **emergency shortcut** only, not the primary CI path.

**Sample data (decided):** use **full** `oscommerce_sample_data.sql` so catalog, cart, and checkout tests match a real demo store.

**Execution split (decided):**

| Style | When | Tooling |
|-------|------|---------|
| **Apache + HTTP** | Setup steps, storefront URLs, session cookies, admin UI, anything `.htaccess`-sensitive | curl or dev HTTP client in PHPUnit |
| **PHP CLI / in-process** | Code paths after install that do not need the web server—e.g. model/services with real PDO against the installed DB | Bootstrap Shop/Admin after install; no Apache |

**Recommended v1 integration catalog (post-Setup, full sample data):**

1. Setup completes; Shop homepage loads (no fatal).
2. Category/product listing includes known sample SKU or title.
3. Session cookie; add sample product to cart.
4. Admin login with installer-created admin.
5. Guest checkout **first step** (payment module stubbed/disabled as needed).

Expand toward full Shop/Admin flows as coverage goals require.

**Out of scope for live I/O:** real PayPal Express charges, outbound geo-IP paid APIs—stub or record fixtures; still cover module code via unit/integration with doubles.

---

## 4. Tooling and repository layout (proposed)

```text
oscommerce/
  composer.json              # dev-only: phpunit/phpunit; + deps only if used
  phpunit.xml.dist           # unit + integration testsuites; coverage config
  documents/test-suite-plan.md
  tests/
    bootstrap.php
    bootstrap-unit.php
    bootstrap-integration.php
    unit/
    integration/
  tools/
    setup-install-harness.php   # automate Setup + full sample data (cloud/CI)
  .cursor/
    install.sh                  # cloud: php8.4-*, mysql client, composer install
```

**Datasmith `tools/test-sandbox` (optional local mirror of cloud):**

- Profile **`oscommerce-mysql`** (Apache + PHP + MySQL), analogous to `zencart-mysql`.
- Local dev convenience; **baselines for integration** still recorded from **cloud agent** runs.

**Matrix promotion path:**

1. `runnable=maybe` when `phpunit.xml.dist` exists and **unit** suite runs on host.
2. `runnable=yes` when **unit** **0F/0E** on host **and** **integration** **0F/0E** on **Linux cloud** (separate baseline row or notes field for integration command).
3. Notes: `vendor/bin/phpunit --testsuite unit`; integration: cloud-only command + `@group integration`.

---

## 5. Phased delivery

### Phase 0 — Foundation (1–2 days)

- Dev-only `composer.json` with `phpunit/phpunit` ^10|^11 (no extra packages until needed).
- `phpunit.xml.dist` with `unit` testsuite and coverage `<source>` for `osCommerce/OM`.
- Modernize `HTMLTest` → `tests/unit/Core/HTMLTest.php`.
- Unit bootstrap without full `settings.ini` site routing where possible.
- Host: green unit suite + first coverage report.

### Phase 1 — Unit expansion (ongoing)

- Grow unit tests across **Core** and **Site** until coverage trends toward **100%** of `osCommerce/OM` (uncovered files tracked in CI).
- Test doubles for `Registry::get('PDO')` and external services where needed.
- PHP 8.4 on `ai-training-20260418`.

### Phase 2 — Cloud LAMP + Setup harness (1–2 weeks)

- Cloud agent (or sandbox): Apache, MySQL, PHP 8.4.
- **`setup-install-harness.php`**: run **Setup** install end-to-end including **full sample data**.
- Integration tests: tag Apache vs CLI in docblocks or `@group apache` / `@group cli`.
- Env vars: `OSCOMMERCE_BASE_URL`, admin credentials from install output.

### Phase 3 — Full suite, coverage, baseline (ongoing)

- Close coverage gaps (generated report per grind); integration catalog grows with Shop/Admin surface.
- **Tiering:**
  - **Tier 1:** unit + coverage (host, seconds–minutes).
  - **Tier 2:** cloud smoke after install (~minutes).
  - **Tier 3:** full integration + coverage on cloud (~longer).
- JUnit + coverage artifacts → `reports/oscommerce/`; `test-baselines.tsv`; `runnable=yes`.

### Phase 4 — Maintenance

- Host: unit gate on every sweep.
- Integration + coverage thresholds: **Linux cloud** on `remote-test-*` grinds.

---

## 6. Risks and mitigations

| Risk | Mitigation |
|------|------------|
| Legacy `OSCOM::initialize()` ties tests to full stack | Split bootstraps; CLI integration after Setup for non-HTTP paths |
| Setup wizard is multi-step HTTP | Dedicated **install harness** in cloud; fail fast with logged step on break |
| Apache-only vs CLI split | Document per-test requirement; `@group apache` for sweep filtering |
| Flaky HTML/date tests | `date.timezone=UTC` in `phpunit.xml`; avoid brittle locale assertions |
| Static `Registry` / session leakage | `tearDown()` resets; patterns from corpus Slim fixes |
| Payment/shipping live credentials | Stubs/fakes; cover code without live charges |
| ~1200 PHP files, 100% coverage | Phased milestones (§7); PCOV/Xdebug on Linux; exclude truly unreachable bootstrap if justified and documented |
| Composer unused bloat | Dev-only manifest; add packages only when referenced |

---

## 7. Success metrics

| Milestone | Metric |
|-----------|--------|
| M0 | `vendor/bin/phpunit --testsuite unit` exits 0 on PHP 8.4 (host) |
| M1 | Unit **0F/0E**; coverage report published; trend line increasing |
| M2 | Cloud: Setup harness + full sample data completes reliably |
| M3 | Integration **0F/0E** on Linux cloud; Apache + CLI groups both green |
| M4 | **100% line coverage** of `oscommerce/OM` (or documented exclusions with issue links) |
| M5 | `test-matrix.tsv`: `runnable=yes`; baseline SHA for unit (host) + integration (cloud) |

---

## 8. Decisions (owner)

| # | Topic | Decision |
|---|--------|----------|
| 8.1 | Integration install | **Run Setup** (automated harness); not SQL-only as default |
| 8.2 | Sample data | **Full** `oscommerce_sample_data.sql` |
| 8.3 | Web server | **Apache** when HTTP/htaccess/routing required; **plain PHP** for tests that do not |
| 8.4 | Composer | **Dev-only** at repo root; add deps only when used |
| 8.5 | Where integration runs | **Linux cloud agent** (canonical); host = unit |

---

## 9. References in this monorepo

- Zen Cart: `tools/test-sandbox/run.php zencart-mysql` (tiered smoke/fast/full).
- Matrix policy: `tools/test-framework-setup.md` (`runnable`, baselines).
- Cloud grinds: `~/.cursor/skills/remote-phpunit-cloud-grind/SKILL.md`.
- **Autonomous cloud brief:** `documents/cloud-grind-agent.md` (paste into Task; no phase-by-phase user prompts).
- Comparable ecommerce sandboxes: `bagisto`, `oxideshop_ce` (MySQL + install).

---

## 10. Cloud grind — run to completion

**Owner intent:** one grind (or chained cloud resumes) implements the full plan through **§7 M4/M5** without stopping to ask “should I continue?”

| Role | Behavior |
|------|----------|
| **Cloud agent** | Follow `documents/cloud-grind-agent.md`: work queue **W0→W6**, fix loops until **0F/0E**, push to `remote-test-*`, track progress in `documents/cloud-grind-status.md`. Stop only at **Finished** or documented **Blocked**. |
| **Host / parent agent** | Preflight (§10.1), launch Task with `environment: cloud`, `run_in_background: true`, entire brief attached. **Do not** ping the user or subagent between phases. Resume cloud Task only if the platform ends the run early — still no “continue?” in the resume prompt; point at `cloud-grind-status.md` and the same Finished criteria. |

Phases in §5 are **ordering for the agent**, not approval gates.

### 10.1 Host preflight (before first Task)

1. Commit `documents/` on `ai-training-20260418` (plan + grind brief).
2. Create/push `remote-test-YYYY-MM-DD` from training tip (cloud clones remote; never rely on unpushed training).
3. Ensure training-tip PHP 8.4 fixes are **on** that pushed branch (merge training into remote-test if needed).

### 10.2 After Finished (host, when user wants baselines)

- Fetch JUnit/coverage from remote SHA; `reports/oscommerce/`; update matrix/baselines per `remote-phpunit-cloud-grind` postflight.

---

*Last updated: 2026-10-05 — corpus planning on `ai-training-20260418`.*
