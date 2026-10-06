# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | PCOV **~45.9%** line coverage on included `osCommerce/OM` (see `coverage-exclusions.md`) |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **114 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** _(pending push)_ on `remote-test-2026-10-05`

**Latest metrics (PHP 8.4.26 Linux):** 114 tests, 356 assertions, **0 failures, 0 errors**. Line coverage **~45.9%** (8695/18945 statements) after documented exclusions; target M4: **100%** of included tree.

**Recent W2 work:** PayPal NVP failure ACK + debug email + process success/failure + Live NVP URL + Authorization action; checkout templates with payment module global; Order `prepOrderID` stale-cart cleanup + `getStatusID`/`remove` static for PHP 8.4; PayPal config upsert via harness DB (`build/install.env` / `127.0.0.1`); `PayPalNvpMock` request capture.

**Next (W2):** Remaining `PayPalExpressCheckout` (zone geo, account optional, SuccessWithWarning redirects), checkout/oscom branches, Order variants/downloads when enabled, more Admin OM3 actions/pages; keep **0F/0E**.

**Blocked (if any):** none
