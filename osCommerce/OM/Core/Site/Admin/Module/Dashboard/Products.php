<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Module\Dashboard;

use Os_Commerce\OM\Core\Access;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
//  require('includes/sites/Admin/applications/products/classes/products.php'); HPDL
class Products extends \Os_Commerce\OM\Core\Site\Admin\Index_Modules_Abstract
{
    public function __construct()
    {
        Registry::get('Language')->load_ini_file('modules/Dashboard/Products.php');
        $this->_title = OSCOM::get_def('admin_indexmodules_products_title');
        $this->_title_link = OSCOM::get_link(null, 'Products');
        if (Access::has_access(OSCOM::get_site(), 'Products')) {
            if (!isset($os_c_currencies)) {
                if (!class_exists('osC_Currencies')) {
                    include 'includes/classes/currencies.php';
                }
                $os_c_currencies = new Os_C_currencies();
            }
            $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' . '  <thead>' . '    <tr>' . '      <th>' . OSCOM::get_def('admin_indexmodules_products_table_heading_products') . '</th>' . '      <th>' . OSCOM::get_def('admin_indexmodules_products_table_heading_price') . '</th>' . '      <th>' . OSCOM::get_def('admin_indexmodules_products_table_heading_date') . '</th>' . '      <th>' . OSCOM::get_def('admin_indexmodules_products_table_heading_status') . '</th>' . '    </tr>' . '  </thead>' . '  <tbody>';
            $Qproducts = Registry::get('PDO')->query('select products_id, greatest(products_date_added, products_last_modified) as date_last_modified from :table_products where parent_id is null order by date_last_modified desc limit 6');
            $Qproducts->execute();
            $counter = 0;
            while ($Qproducts->fetch()) {
                $data = Os_C_products_admin::get($Qproducts->value_int('products_id'));
                $products_icon = osc_icon('products.png');
                $products_price = $data['products_price'];
                if (!empty($data['variants'])) {
                    $products_icon = osc_icon('attach.png');
                    $products_price = null;
                    foreach ($data['variants'] as $variant) {
                        if ($products_price === null || $variant['data']['price'] < $products_price) {
                            $products_price = $variant['data']['price'];
                        }
                    }
                    if ($products_price === null) {
                        $products_price = 0;
                    }
                }
                $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' . '      <td>' . osc_link_object(OSCOM::get_link(null, 'Products', 'id=' . (int) $data['products_id'] . '&action=save'), $products_icon . '&nbsp;' . osc_output_string_protected($data['products_name'])) . '</td>' . '      <td>' . (!empty($data['variants']) ? 'from ' : '') . $os_c_currencies->format($products_price) . '</td>' . '      <td>' . $Qproducts->value('date_last_modified') . '</td>' . '      <td align="center">' . osc_icon((int) $data['products_status'] === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' . '    </tr>';
                $counter++;
            }
            $this->_data .= '  </tbody>' . '</table>';
        }
    }
}