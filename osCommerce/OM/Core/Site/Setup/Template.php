<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Setup;

use osCommerce\OM\Core\OSCOM;

class Template extends \osCommerce\OM\Core\Template
{
    public function __construct()
    {
        $this->set('default');
    }

    public static function getTemplates()
    {
        return [['id' => 0,
                           'code' => 'default']];
    }

    public function set($code = null)
    {
        if (!isset($_SESSION[OSCOM::getSite()]['template'])) {
            $data = [];

            foreach ($this->getTemplates() as $template) {
                $data = ['id' => $template['id'],
                              'code' => $template['code']];
            }

            $_SESSION[OSCOM::getSite()]['template'] = $data;
        }

        $this->_template_id = $_SESSION[OSCOM::getSite()]['template']['id'];
        $this->_template = $_SESSION[OSCOM::getSite()]['template']['code'];
    }
}
