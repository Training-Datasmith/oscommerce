# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18500** lines (PayPal exempt); **~64.8%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **151 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `pending` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 151 tests, 398 assertions, **0 failures, 0 errors**. Line coverage **~64.79%** (**11986/18500** statements) on included tree (`coverage-exclusions.md`; PayPal exempt).

**Recent W2 work:** Customers Save `changed` boolean fix + new_address/deleteAB flows; Modules install/remove + multi-page layout ctor; importDB in-process test; product_listing prefers manufacturer products.

**Next (W2):** `Customers/Save/Process` through `saveAddress` success, `Modules.php` ctor cache miss cleanup branches, `importDB` service depends/precedes arrays, `ShoppingCart` remainder — keep **0F/0E**.

**Blocked (if any):** none
