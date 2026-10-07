# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | M4 denominator **~18501** lines (PayPal exempt); **~68.8%** covered |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **166 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** (pending push) on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux, DB 127.0.0.1):** 166 tests, 417 assertions, **0 failures, 0 errors**. Line coverage **~68.77%** (**12723/18501**).

**Recent W2 work:** Admin `batch_*` POST seeding + dedicated sweep; Product/ShoppingCart variant paths; checkout multi-method shipping quotes; CoreUpdate phar fixture + `phar.readonly=0` in phpunit; Setup Index `main.php` + step_3 host/port URL.

**Partial / exempt:** `importDB::execute()` full ImportSQL + FK only on isolated DB; harness uses `executeHarnessSafePostImport()` / `ImportDbPartialRunner`. PayPal paths exempt per `coverage-exclusions.md`.

**Next (W2):** `step_3.php` / Setup Index remainder, Admin `Categories`/`PaymentModules` pages, `ShoppingCart`/`Product.php` gaps, checkout `main.php`/`billing.php`, shop templates — keep **0F/0E**.

**Blocked (if any):** none
