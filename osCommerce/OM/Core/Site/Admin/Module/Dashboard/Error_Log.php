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
use Os_Commerce\OM\Core\DateTime;
use Os_Commerce\OM\Core\Error_Handler;
use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
use Os_Commerce\OM\Core\Registry;
class Error_Log extends \Os_Commerce\OM\Core\Site\Admin\Index_Modules_Abstract
{
    public function __construct()
    {
        $OSCOM_Language = Registry::get('Language');
        $OSCOM_Template = Registry::get('Template');
        $OSCOM_Language->load_ini_file('modules/Dashboard/ErrorLog.php');
        $this->_title = OSCOM::get_def('admin_dashboard_module_errorlog_title');
        $this->_title_link = OSCOM::get_link(null, 'ErrorLog');
        if (Access::has_access(OSCOM::get_site(), 'ErrorLog')) {
            $this->_data = '<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">' . '  <thead>' . '    <tr>' . '      <th>' . OSCOM::get_def('admin_dashboard_module_errorlog_table_heading_date') . '</th>' . '      <th>' . OSCOM::get_def('admin_dashboard_module_errorlog_table_heading_message') . '</th>' . '    </tr>' . '  </thead>' . '  <tbody>';
            if (Error_Handler::get_total_entries() > 0) {
                $counter = 0;
                foreach (Error_Handler::get_all(6) as $row) {
                    $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');"' . ($counter % 2 ? ' class="alt"' : '') . '>' . '      <td style="white-space: nowrap;">' . $OSCOM_Template->get_icon(16, 'errorlog.png') . '&nbsp;' . DateTime::get_short(DateTime::from_unix_timestamp($row['timestamp']), true) . '</td>' . '      <td>' . HTML::output_protected(substr($row['message'], 0, 60)) . '..</td>' . '    </tr>';
                    $counter++;
                }
            } elseif (!is_writable(OSCOM::BASE_DIRECTORY . 'Work/Database/')) {
                $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');">' . '      <td colspan="2">' . HTML::icon('cross.png') . '&nbsp;' . sprintf(OSCOM::get_def('admin_dashboard_module_errorlog_not_writable'), OSCOM::BASE_DIRECTORY . 'Work/Database/') . '</td>' . '    </tr>';
            } else {
                $this->_data .= '    <tr onmouseover="$(this).addClass(\'mouseOver\');" onmouseout="$(this).removeClass(\'mouseOver\');">' . '      <td colspan="2">' . HTML::icon('tick.png') . '&nbsp;' . OSCOM::get_def('admin_dashboard_module_errorlog_no_errors_found') . '</td>' . '    </tr>';
            }
            $this->_data .= '  </tbody>' . '</table>';
        }
    }
}