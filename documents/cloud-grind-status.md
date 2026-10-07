# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18495** lines (PayPal exempt); **~53%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **134 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `038363bf` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 134 tests, 379 assertions, **0 failures, 0 errors**. Line coverage **~53%** (**9807/18495** statements) on included tree (`coverage-exclusions.md`; PayPal exempt).

**Recent W2 work:** Languages `Import` full placeholder loops (fixed 5-char `code`); Mail MIME `send()` build path via `OSCOM_TEST_MAIL_BUILD_COVERAGE`; checkout Process success + Shipping null-module guard; ShoppingCart protected sweep; PHP 8.4 `Mail::uniqid` fix.

**Next (W2):** Checkout/admin page templates (`oscom.php`, billing/shipping), `ShoppingCart` remainder, `importDB` (needs DB create privilege or partial harness), `ErrorHandler`, low-% admin pages — keep **0F/0E**.

**Blocked (if any):** none
