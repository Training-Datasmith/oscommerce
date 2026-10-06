# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | PCOV **~45.7%** line coverage on included `osCommerce/OM` (see `coverage-exclusions.md`) |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **103 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `4c0fd01d`

**Latest metrics (PHP 8.4.26 Linux):** 103 tests, 339 assertions, **0 failures, 0 errors**. Line coverage **~45.7%** (8653/18945 statements) after documented exclusions; target M4: **100%** of included tree.

**Recent W2 work:** PayPal NVP mock + `initializeExpressCheckout` success redirect + instant-update RPC callback; `Order::remove` (status 4) + `prepOrderID` reuse + `process` stock path; OM3 admin page includes + editor routes; expanded checkout/account oscom layout; shop route discovery skips `Callback` and batches in-process routes (avoids child-process exit/OOM).

**Next (W2):** Deeper `PayPalExpressCheckout` (failure ACK, retrieve/process, live URL branch), checkout templates, `Order` download/variant branches, remaining Shop modules and Admin OM3 actions; keep **0F/0E**.

**Blocked (if any):** none
