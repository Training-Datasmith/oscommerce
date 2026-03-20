<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Setup;

use Os_Commerce\OM\Core\OSCOM;
class Template extends \Os_Commerce\OM\Core\Template
{
    public function __construct()
    {
        $this->set('default');
    }
    public static function get_templates()
    {
        return [['id' => 0, 'code' => 'default']];
    }
    public function set($code = null)
    {
        if (!isset($_SESSION[OSCOM::get_site()]['template'])) {
            $data = [];
            foreach ($this->get_templates() as $template) {
                $data = ['id' => $template['id'], 'code' => $template['code']];
            }
            $_SESSION[OSCOM::get_site()]['template'] = $data;
        }
        $this->_template_id = $_SESSION[OSCOM::get_site()]['template']['id'];
        $this->_template = $_SESSION[OSCOM::get_site()]['template']['code'];
    }
}