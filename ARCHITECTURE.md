# osCommerce (OM) Architecture

## Purpose

osCommerce Online Merchant (OM branch) is a PHP e-commerce platform with a service-container approach introduced in the `osCommerce\OM` namespace. It targets PHP 5.3+ and provides both storefront and admin sites.

## Directory Structure

```
oscommerce/
└── osCommerce/
    └── OM/
        └── Core/
            ├── Site/
            │   ├── Admin/          # Admin panel: pages, controllers, languages
            │   │   └── languages/  # en_US translation files per module
            │   └── Shop/           # Storefront application
            ├── Service/            # Service modules (Session, Language, Currencies…)
            └── *.php               # Core framework classes (OSCOM, Registry, DB…)
```

## Key Design Decisions

- **Registry pattern**: `OSCOM\OM\Registry` is a global object store keyed by string. All shared services (DB, Session, Language) are registered here at bootstrap.
- **Site/Module architecture**: Each "site" (Admin, Shop) has its own controller and access-control module classes. Modules declare `install()`, `remove()`, `keys()` methods that manage `configuration` table rows.
- **Language files**: Translation strings are PHP files that define constants (e.g. `HEADING_TITLE`). Each site has an `en_US/` directory with per-page and per-module files.
- **Service layer**: Services like `Session`, `Language`, `Currencies` are loaded via the module system and registered into the Registry.
- **No Composer**: Dependencies are bundled. The `OM/` namespace is bootstrapped by a custom autoloader.

## Extension Points

- Add a dashboard module: create `Core/Site/Admin/languages/en_US/modules/Dashboard/{Name}.php` and the corresponding module class.
- Add a service: implement the service interface and register it in the site's service list.
- Add an access module: create under `modules/access/` with the standard `install()`/`remove()` contract.

## Dependency Flow

```
index.php
  → OSCOM::initialize()
  → Registry::set('DB', ...), Registry::set('Session', ...)
  → Site controller (Admin or Shop)
  → Module execution
  → Template output
```
