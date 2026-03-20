<?php

declare (strict_types=1);
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
class Os_C_application_products_actions_save extends Os_C_application_products
{
    public function __construct()
    {
        global $os_c_language, $os_c_message_stack;
        parent::__construct();
        $this->_page_contents = 'edit.php';
        if ((osc_empty(CFG_APP_IMAGEMAGICK_CONVERT) || !file_exists(CFG_APP_IMAGEMAGICK_CONVERT)) && !Os_C_image_admin::has_gd_support()) {
            $os_c_message_stack->add('header', $os_c_language->get('ms_warning_image_processor_not_available'), 'warning');
        }
        if (isset($_POST['subaction']) && $_POST['subaction'] == 'confirm') {
            $error = false;
            $data = ['quantity' => isset($_POST['products_quantity']) ? $_POST['products_quantity'] : 0, 'price' => is_numeric($_POST['products_price']) ? $_POST['products_price'] : 0, 'weight' => isset($_POST['products_weight']) ? $_POST['products_weight'] : 0, 'weight_class' => isset($_POST['products_weight_class']) ? $_POST['products_weight_class'] : '', 'status' => $_POST['products_status'], 'model' => isset($_POST['products_model']) ? $_POST['products_model'] : '', 'tax_class_id' => $_POST['products_tax_class_id'], 'products_name' => $_POST['products_name'], 'products_description' => $_POST['products_description'], 'products_keyword' => $_POST['products_keyword'], 'products_tags' => $_POST['products_tags'], 'products_url' => $_POST['products_url']];
            if (isset($_POST['attributes'])) {
                $data['attributes'] = $_POST['attributes'];
            }
            if (isset($_POST['categories'])) {
                $data['categories'] = $_POST['categories'];
            }
            if (isset($_POST['localimages'])) {
                $data['localimages'] = $_POST['localimages'];
            }
            if (isset($_POST['variants_tax_class_id'])) {
                $data['variants_tax_class_id'] = $_POST['variants_tax_class_id'];
            }
            if (isset($_POST['variants_price'])) {
                $data['variants_price'] = $_POST['variants_price'];
            }
            if (isset($_POST['variants_model'])) {
                $data['variants_model'] = $_POST['variants_model'];
            }
            if (isset($_POST['variants_quantity'])) {
                $data['variants_quantity'] = $_POST['variants_quantity'];
            }
            if (isset($_POST['variants_combo'])) {
                $data['variants_combo'] = $_POST['variants_combo'];
            }
            if (isset($_POST['variants_combo_db'])) {
                $data['variants_combo_db'] = $_POST['variants_combo_db'];
            }
            if (isset($_POST['variants_weight'])) {
                $data['variants_weight'] = $_POST['variants_weight'];
            }
            if (isset($_POST['variants_weight_class'])) {
                $data['variants_weight_class'] = $_POST['variants_weight_class'];
            }
            if (isset($_POST['variants_status'])) {
                $data['variants_status'] = $_POST['variants_status'];
            }
            if (isset($_POST['variants_default_combo'])) {
                $data['variants_default_combo'] = $_POST['variants_default_combo'];
            }
            foreach ($data['products_keyword'] as $value) {
                if (empty($value)) {
                    $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_product_keyword_empty'), 'error');
                    $error = true;
                } elseif (preg_match('/^[a-z0-9_-]+$/iD', $value) !== 1) {
                    $os_c_message_stack->add($this->_module, sprintf($os_c_language->get('ms_error_product_keyword_invalid'), osc_output_string_protected($value)), 'error');
                    $error = true;
                }
                if (Os_C_products_admin::get_keyword_count($value, isset($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) ? $_GET[$this->_module] : null) > 0) {
                    $os_c_message_stack->add($this->_module, sprintf($os_c_language->get('ms_error_product_keyword_exists'), osc_output_string_protected($value)), 'error');
                    $error = true;
                }
            }
            if ($error === false) {
                if (Os_C_products_admin::save(isset($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) ? $_GET[$this->_module] : null, $data)) {
                    $os_c_message_stack->add($this->_module, $os_c_language->get('ms_success_action_performed'), 'success');
                } else {
                    $os_c_message_stack->add($this->_module, $os_c_language->get('ms_error_action_not_performed'), 'error');
                }
                osc_redirect_admin(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&cID=' . $_GET['cID']));
            }
        }
    }
}