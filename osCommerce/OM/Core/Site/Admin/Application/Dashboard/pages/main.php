<?php

/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Site\Admin\Application\Dashboard\Dashboard;
?>

<h1><?php 
echo $OSCOM_Template->get_icon(32) . HTML::link(OSCOM::get_link(), $OSCOM_Template->get_page_title());
?></h1>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php 
$col = 0;
$modules = Dashboard::get_modules();
foreach ($modules as $module) {
    if ($col === 0) {
        echo '  <tr>' . "\n";
    }
    $col++;
    if ($col <= 2) {
        echo '    <td width="50%" valign="top">' . "\n";
    }
    echo '<h2>';
    if (isset($module['link'])) {
        echo '<a href="' . $module['link'] . '">';
    }
    echo $module['title'];
    if (isset($module['link'])) {
        echo '</a>';
    }
    echo '</h2>';
    echo $module['data'];
    if ($col <= 2) {
        echo '    </td>' . "\n";
    }
    if (next($modules) === false || $col === 2) {
        $col = 0;
        echo '  </tr>' . "\n";
    }
}
?>

</table>
