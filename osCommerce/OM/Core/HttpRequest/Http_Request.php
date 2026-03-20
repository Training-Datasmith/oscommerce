<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Http_Request;

class Http_Request
{
    protected static $_methods = ['get' => HTTP_METH_GET, 'post' => HTTP_METH_POST];
    public static function execute($parameters)
    {
        $h = new \Http_Request($parameters['server']['scheme'] . '://' . $parameters['server']['host'] . $parameters['server']['path'] . (isset($parameters['server']['query']) ? '?' . $parameters['server']['query'] : ''), static::$_methods[$parameters['method']], ['redirect' => 5]);
        if ($parameters['method'] == 'post') {
            $post_params = [];
            parse_str($parameters['parameters'], $post_params);
            $h->set_post_fields($post_params);
        }
        $h->send();
        return $h->get_response_body();
    }
    public static function can_use()
    {
        return class_exists('\HttpRequest');
    }
}