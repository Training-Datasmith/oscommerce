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
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
/**
 * @since v3.0.2
 */
class Customers extends \Os_Commerce\OM\Core\Site\Admin\Index_Modules_Abstract
{
    public function __construct()
    {
        Registry::get('Language')->load_ini_file('modules/Dashboard/Customers.php');
        $this->_title = OSCOM::get_def('admin_indexmodules_customers_title');
        $this->_title_link = OSCOM::get_link(null, 'Customers');
        if (Access::has_access(OSCOM::get_site(), 'Customers')) {
            $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' . '  <thead>' . '    <tr>' . '      <th>' . OSCOM::get_def('admin_indexmodules_customers_table_heading_customers') . '</th>' . '      <th>' . OSCOM::get_def('admin_indexmodules_customers_table_heading_date') . '</th>' . '      <th>' . OSCOM::get_def('admin_indexmodules_customers_table_heading_status') . '</th>' . '    </tr>' . '  </thead>' . '  <tbody>';
            $Qcustomers = Registry::get('PDO')->query('select customers_id, customers_gender, customers_lastname, customers_firstname, customers_status, date_account_created from :table_customers order by date_account_created desc limit 6');
            $Qcustomers->execute();
            $counter = 0;
            while ($Qcustomers->fetch()) {
                $customer_icon = HTML::icon('people.png');
                if (ACCOUNT_GENDER > -1) {
                    switch ($Qcustomers->value('customers_gender')) {
                        case 'm':
                            $customer_icon = HTML::icon('user_male.png');
                            break;
                        case 'f':
                            $customer_icon = HTML::icon('user_female.png');
                            break;
                    }
                }
                $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' . '      <td>' . HTML::link(OSCOM::get_link(null, 'Customers', 'Save&id=' . $Qcustomers->value_int('customers_id')), $customer_icon . '&nbsp;' . $Qcustomers->value_protected('customers_firstname') . ' ' . $Qcustomers->value_protected('customers_lastname')) . '</td>' . '      <td>' . $Qcustomers->value('date_account_created') . '</td>' . '      <td align="center">' . HTML::icon($Qcustomers->value_int('customers_status') === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null) . '</td>' . '    </tr>';
                $counter++;
            }
            $this->_data .= '  </tbody>' . '</table>';
        }
    }
}