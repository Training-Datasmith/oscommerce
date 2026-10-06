# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | PCOV **~39.3%** line coverage on included `osCommerce/OM` (see `coverage-exclusions.md`) |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **85 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** _(pending push to `remote-test-2026-10-05`)_

**Latest metrics (PHP 8.4.26 Linux):** 85 tests, 310 assertions, **0 failures, 0 errors**. Line coverage **~39.25%** (7479/19054 statements) after documented exclusions; target M4: **100%** of included tree.

**Recent W2 work:** Shop/Admin page includes, module pages, Application/Core SQL sweeps, Setup RPC + Install models, ShoppingCart method sweep, checkout address seeding, expanded Shop/Admin routes.

**Next (W2):** Continue closing gap on `Template`/checkout pages, `Order`/`Product` branches, Setup install steps, remaining Admin OM3 editors; keep **0F/0E**.

**Blocked (if any):** none
