# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | PCOV line coverage **~26.9%** on included `osCommerce/OM` (legacy admin + SQL Server paths excluded per `coverage-exclusions.md`) |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **75 tests, 0 failures, 0 errors** (warnings/deprecations from legacy code) |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** _(pending push)_

**Latest metrics (PHP 8.4.26 Linux):** 75 tests, 299 assertions, **0 failures, 0 errors**. Line coverage **~26.87%** (5168/19235 statements) after documented exclusions; target M4: **100%** of included tree.

**Recent W2 work:** In-process Shop/Admin/Setup route rendering (`InProcessSiteRenderer`), HTTP action matrix, SQL/module sweeps, Shop model/module/assets coverage tests; PHP 8.4 fixes (`HTML::outputProtected`, `Access` custom apps dir, `OSCOM::redirect` test hook, `%d` email language string); legacy osC2 admin apps excluded from coverage denominator.

**Next (W2):** Close remaining ~73% gap on included OM3 code (deeper checkout/account flows, Admin CRUD pages, `ShoppingCart`/`Template` branches, Setup RPC classes); keep **0F/0E**.

**Blocked (if any):** none
