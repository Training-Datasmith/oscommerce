<?php

declare(strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\RPC;

use osCommerce\OM\Core\HTML;
use osCommerce\OM\Core\OSCOM;

class Controller implements \osCommerce\OM\Core\SiteInterface
{
    public const STATUS_SUCCESS = 1;
    public const STATUS_NO_SESSION = -10;
    public const STATUS_NO_MODULE = -20;
    public const STATUS_NO_ACCESS = -50;
    public const STATUS_CLASS_NONEXISTENT = -60;
    public const STATUS_NO_ACTION = -70;
    public const STATUS_ACTION_NONEXISTENT = -71;

    protected static $_default_application = 'Index';

    public static function initialize()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_GET)) {
            echo json_encode(['rpcStatus' => self::STATUS_NO_MODULE]);
            exit;
        }

        $site = HTML::sanitize(basename(key(array_slice($_GET, 1, 1, true))));
        $application = HTML::sanitize(basename(key(array_slice($_GET, 2, 1, true))));

        if (!OSCOM::siteExists($site)) {
            echo json_encode(['rpcStatus' => self::STATUS_CLASS_NONEXISTENT]);
            exit;
        }

        OSCOM::setSite($site);

        if (!OSCOM::siteApplicationExists($application)) {
            echo json_encode(['rpcStatus' => self::STATUS_CLASS_NONEXISTENT]);
            exit;
        }

        OSCOM::setSiteApplication($application);

        call_user_func(['osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller', 'initialize']);

        if (!call_user_func(['osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller', 'hasAccess'], $application)) {
            echo json_encode(['rpcStatus' => self::STATUS_NO_ACCESS]);
            exit;
        }

        if (count($_GET) < 3) {
            echo json_encode(['rpcStatus' => self::STATUS_NO_ACTION]);
            exit;
        }

        $rpc_called = false;

        $rpc = ['RPC'];

        for ($i = 3, $n = count($_GET); $i < $n; $i++) {
            $subrpc = HTML::sanitize(basename(key(array_slice($_GET, $i, 1, true))));

            if (self::siteApplicationRPCExists(implode('\\', $rpc) . '\\' . $subrpc)) {
                call_user_func(['osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\' . implode('\\', $rpc) . '\\' . $subrpc, 'execute']);

                $rpc[] = $subrpc;

                $rpc_called = true;
            } else {
                break;
            }
        }

        if ($rpc_called === false) {
            echo json_encode(['rpcStatus' => self::STATUS_NO_ACTION]);
            exit;
        }

        exit;
    }

    public static function getDefaultApplication()
    {
        return self::$_default_application;
    }

    public static function hasAccess($application)
    {
        return true;
    }

    public static function siteApplicationRPCExists($rpc)
    {
        return class_exists('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Application\\' . OSCOM::getSiteApplication() . '\\' . $rpc);
    }
}
