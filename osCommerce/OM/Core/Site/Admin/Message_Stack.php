<?php

declare (strict_types=1);
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
namespace Os_Commerce\OM\Core\Site\Admin;

use Os_Commerce\OM\Core\HTML;
use Os_Commerce\OM\Core\OSCOM;
class Message_Stack extends \Os_Commerce\OM\Core\Message_Stack
{
    public function get($group = null)
    {
        if (empty($group)) {
            $group = OSCOM::get_site_application();
        }
        $result = false;
        if ($this->exists($group)) {
            $data = [];
            foreach ($this->_data[$group] as $message) {
                $data['messageStack' . ucfirst($message['type'])][] = $message['text'];
            }
            $result = '';
            foreach ($data as $type => $messages) {
                $result .= '<div class="' . HTML::output_protected($type) . '" onmouseover="$(this).find(\'span:first\').show();" onmouseout="$(this).find(\'span:first\').hide();"><span style="float: right; display: none;"><a href="#" onclick="$(this).parent().parent().slideFadeToggle();">' . HTML::icon('minimize.png', 'Hide') . '</a></span>';
                foreach ($messages as $message) {
                    $result .= '<p>' . HTML::output_protected($message) . '</p>';
                }
                $result .= '</div>';
            }
            unset($this->_data[$group]);
        }
        return $result;
    }
}