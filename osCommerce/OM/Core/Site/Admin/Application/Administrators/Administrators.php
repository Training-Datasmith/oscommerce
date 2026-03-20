<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin\Application\Administrators;

class Administrators extends \Os_Commerce\OM\Core\Application_Model_Abstract
{
    public const ACCESS_MODE_ADD = 'add';
    public const ACCESS_MODE_SET = 'set';
    public const ACCESS_MODE_REMOVE = 'remove';
}