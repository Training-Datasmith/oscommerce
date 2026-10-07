# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **18490** lines (PayPal exempt); **~51.7%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **130 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** _(pending push)_ on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 130 tests, 373 assertions, **0 failures, 0 errors**. Line coverage **~51.7%** (**9560/18490** statements) on included tree after `coverage-exclusions.md` (PayPal exempt — do not grind).

**Recent W2 work:** Languages `Import` harness test + payload helper; checkout `Process` POST via `CheckoutProcessPost`; ShoppingCart `_calculate`; Admin Service modules + CoreUpdate models; oscom template route sweep; Core Upload/CreditCard/DateTime; admin customer section globals.

**Next (W2):** Finish `Languages/Import.php` placeholder loops, checkout pages + `Process` success paths, `ShoppingCart`/`Mail`/`importDB` (non-destructive harness), remaining low-% files from PCOV — keep **0F/0E**.

**Blocked (if any):** none — prior “Blocked” label was incorrect; grind continues toward M4.
