# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18501** lines (PayPal exempt); **~71.2%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **182 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `7db41218` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 182 tests, 435 assertions, **0 failures, 0 errors**. Line coverage **~71.16%** (**13165/18501**).

**Recent W2 work:** `OSCOM_PUBLIC_BASE_DIRECTORY` for Setup step_3; Setup scope extract; `CoverageConfirmation` payment module + DB payment modules seed; checkout main/billing confirmation branches; CreditCards batch seeder fix (`id` column); product listing / Search / listing integration test; `HarnessSettingsFixture` restores `settings.ini` after Setup step_3 tests.

**Partial / exempt:** `importDB::execute()` full ImportSQL + FK only on isolated DB; harness uses `executeHarnessSafePostImport()` / `ImportDbPartialRunner`. PayPal paths exempt per `coverage-exclusions.md`.

**Next (W2):** Admin Categories depth, `product_listing` / shop `oscom.php`, Account templates, remaining Setup `step_3` readonly branch — keep **0F/0E**.

**Blocked (if any):** none
