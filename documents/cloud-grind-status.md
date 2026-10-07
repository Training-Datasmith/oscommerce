# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **18490** lines (PayPal exempt); **~48.3%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **124 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `4a0419b3` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 124 tests, 367 assertions, **0 failures, 0 errors**. Line coverage **~48.3%** (**8924/18490** statements) on included tree after `coverage-exclusions.md` (incl. **PayPal** paths — do not grind).

**Recent W2 work:** Documented PayPal exclusions in `phpunit.xml.dist` + `coverage-exclusions.md`; Shop/Admin Process action coverage; Core Mail/DateTime/Language/Modules; account + catalog page sweeps; shop domain deep sweep.

**Next (W2):** Largest remaining gaps (non-PayPal): `Languages/.../Import.php`, checkout/account **Process** completion paths, `ShoppingCart.php`, checkout/admin **pages** + `oscom.php`, `Mail` send body paths, Setup `importDB`, Admin `CoreUpdate` models — keep **0F/0E**.

**Blocked (if any):** none
