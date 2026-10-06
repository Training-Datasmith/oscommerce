# osCommerce test suite

Planning: `documents/test-suite-plan.md`. Cloud grind policy: `documents/cloud-grind-agent.md`.

## Prerequisites

- PHP **8.4** with `pdo_mysql`, `mbstring`, `xml`, `gd`, `curl`
- **Unit only:** `composer install` at repo root
- **Integration:** Linux LAMP per `.cursor/install.sh` (Apache docroot = repo root, MySQL, `tools/setup-install-harness.php`)

## Commands

```bash
# Install dev dependencies
composer install

# Unit tests (no database)
vendor/bin/phpunit --testsuite unit

# Fresh Setup install + full sample data (integration prerequisite)
php tools/setup-install-harness.php

# Integration tests (@group integration; Apache HTTP)
vendor/bin/phpunit --testsuite integration

# Full suite
vendor/bin/phpunit

# Coverage (PCOV on Linux)
php -d pcov.directory=/workspace/osCommerce/OM vendor/bin/phpunit --coverage-text

# JUnit XML (CI / baselines)
vendor/bin/phpunit --log-junit phpunit-remote-$(date +%Y%m%d).xml
```

Environment overrides for harness/integration: `OSCOMMERCE_BASE_URL`, `OSCOMMERCE_DB_*`, `OSCOMMERCE_ADMIN_*` (see `build/install.env` after harness).
