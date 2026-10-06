# Coverage exclusions

Document any `osCommerce/OM` paths excluded from the 100% line-coverage goal (with rationale). Each path must also appear in `phpunit.xml.dist` `<source><exclude>`.

| Path / pattern | Reason |
|----------------|--------|
| `osCommerce/OM/Core/Site/Admin/Application/{administrators_log,backup,banner_manager,cache,file_manager,image_groups,images,manufacturers,modules_geoip,modules_order_total,modules_shipping,newsletters,orders,orders_status,product_attributes,products,products_expected,product_types,product_variants,reviews,specials,statistics,templates,templates_modules,templates_modules_layout,weight_classes,whos_online}/` | Orphan osCommerce 2.x admin apps (no OM3 `Controller.php`, broken `includes/applications/*` requires); not reachable via `index.php` routing on OM 3. |
| `osCommerce/OM/Core/Site/Admin/Application/Login/SQL/Microsoft/` | Microsoft SQL Server driver; CI/harness uses MySQL only. |
| `osCommerce/OM/Core/Site/Admin/Application/Countries/SQL/Microsoft/` | Microsoft SQL Server driver; CI/harness uses MySQL only. |
| `osCommerce/OM/Tests`, `osCommerce/OM/External`, `osCommerce/OM/Work` | Test harness, vendored external, runtime work dirs (see `phpunit.xml.dist`). |
