# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18495** lines (PayPal exempt); **~64.6%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **146 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** (pending push) on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 146 tests, 393 assertions, **0 failures, 0 errors**. Line coverage **~64.57%** (**11942/18495** statements) on included tree (`coverage-exclusions.md`; PayPal exempt).

**Recent W2 work:** `dispatch()` + oscom layout use `extract()` scope; template header/footer/box flags; `includeRenderedShopOscomLayout()`; importDB module stack partial runner; Customers Save/Process validation/delete paths; Mail CC/BCC/charset; product_listing seed improvements.

**Next (W2):** `importDB.php` lines via safe invoke, `product_listing.php` + `Modules.php`, push `Customers/Save/Process` success path, `oscom.php` HPDL/legacy branches — keep **0F/0E**.

**Blocked (if any):** none
