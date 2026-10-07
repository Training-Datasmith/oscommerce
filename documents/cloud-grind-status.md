# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18500** lines (PayPal exempt); **~64.7%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **149 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `42a28261` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 149 tests, 396 assertions, **0 failures, 0 errors**. Line coverage **~64.72%** (**11974/18500** statements) on included tree (`coverage-exclusions.md`; PayPal exempt).

**Recent W2 work:** `importDB::executeHarnessSafePostImport` + partial methods (PCOV on importDB.php); legacy HPDL oscom layout helper; product_listing empty/manufacturer variants; Modules `isInstalled`; Customers Save success POST with DB email.

**Next (W2):** `Customers/Save/Process` success + address loops, `product_listing.php` column matrix, `Modules.php` install/remove, `importDB` remainder — keep **0F/0E**.

**Blocked (if any):** none
