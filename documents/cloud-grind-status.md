# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | PCOV **~45%** line coverage on included `osCommerce/OM` (see `coverage-exclusions.md`) |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **96 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `5f5ad3ec` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux):** 96 tests, 326 assertions, **0 failures, 0 errors**. Line coverage **~45%** (8537+/18945 statements) after documented exclusions; target M4: **100%** of included tree.

**Recent W2 work:** `OSCOM_TEST_HTTP_MOCK` + PayPal NVP mock; `Order::insert`/`process` lifecycle test; `setBillingMethod(..., false)` + session currency seed; PayPal/Order PHP 8.4 trim/wordwrap fixes; `OSCOM_TEST_SKIP_MAIL`; Admin customer sections + harness customer id in admin routes; expanded account/checkout oscom layout pages.

**Next (W2):** Continue PayPal branches (retrieve redirect, instant update), remaining page templates, Admin editors, legacy modules; keep **0F/0E**.

**Blocked (if any):** none
