# Cloud grind status

Updated by the cloud agent during autonomous runs. Host resumes read this file and continue the work queue in `cloud-grind-agent.md` without asking the user.

| Step | Status | SHA / notes |
|------|--------|-------------|
| W0 LAMP install | done | install.sh exit 0; PHP 8.4 + pcov + composer + Apache + MySQL |
| W1 Phase 0 unit | done | HTMLTest ported; `vendor/bin/phpunit --testsuite unit` 0F/0E |
| W2 Unit coverage → 100% | in progress | ~1.0% line coverage (PCOV); expand Core/Site unit tests + integration paths |
| W3 Setup harness + sample data | done | `php tools/setup-install-harness.php` exit 0; `build/install.env` |
| W4 Integration 0F/0E | done | `ShopInstallSmokeTest` (apache); 2 tests green after PHP 8.4 shop fixes |
| W5 JUnit / coverage artifacts | done | `phpunit-remote-20261006.xml` at repo root |
| W6 Run documentation | done | `tests/README.md` |

**Branch tip:** `a5b44094`

**Latest metrics (PHP 8.4.26 Linux):** 30 tests, 45 assertions, 0 failures, 0 errors, 1 deprecation (strftime in `HTML::dateSelectMenu`). Line coverage ~1.0% of `osCommerce/OM` (target M4: 100%).

**Next (W2):** Grow unit tests; fix remaining PHP 8.4 strictness on uncovered paths; re-run PCOV until 100% minus documented exclusions.

**Blocked (if any):** none
