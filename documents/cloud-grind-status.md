# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18501** lines (PayPal exempt); **~69.9%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **174 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `71c4272e` on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 174 tests, 425 assertions, **0 failures, 0 errors**. Line coverage **~69.44%** (**12847/18501**).

**Recent W2 work:** Categories RPC GetAll/SaveSortOrder + main.php; shop `oscom.php` layout branches (box modules off, logged-in confirm); Product `checkEntry`/variants/`incrementCounter`; cart update/remove logged-in; Setup Index language sweep; step_3 POST array hidden fields.

**Partial / exempt:** `importDB::execute()` full ImportSQL + FK only on isolated DB; harness uses `executeHarnessSafePostImport()` / `ImportDbPartialRunner`. PayPal paths exempt per `coverage-exclusions.md`.

**Next (W2):** `step_3.php` / Setup Index remainder, checkout `main.php` gaps, Products/Search templates, Admin Services edit — keep **0F/0E**.

**Blocked (if any):** none
