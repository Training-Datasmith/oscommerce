# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | PCOV **~43.8%** line coverage on included `osCommerce/OM` (see `coverage-exclusions.md`) |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | HTTP + in-process integration; **92 tests, 0 failures, 0 errors** |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** _(push after this commit)_

**Latest metrics (PHP 8.4.26 Linux):** 92 tests, 320 assertions, **0 failures, 0 errors**. Line coverage **~43.78%** (8292/18940 statements) after documented exclusions; target M4: **100%** of included tree.

**Recent W2 work:** `ShopHarnessDataSeeder` (customer/address/order rows), `includeShopPageViaOscomLayout`, guest checkout payment/billing seed, `ShopLayoutCoverageTest`, `ShopOrderDeepCoverageTest`, `SessionMemcacheCoverageTest`, PayPal/COD payment module sweep, account routes with real `order_id`.

**Next (W2):** PayPalExpressCheckout deep paths (HTTP mocks), legacy Admin apps still excluded; drive remaining page templates and `Order::insert`/`process`; SQL/Microsoft already excluded; keep **0F/0E**.

**Blocked (if any):** none
