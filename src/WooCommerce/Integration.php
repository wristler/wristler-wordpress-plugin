<?php

namespace Wristler\WooCommerce;

use WC_Admin_Settings;

class Integration extends \WC_Integration
{

    public function __construct()
    {
        $this->id = 'wristler';
        $this->method_title = __('Wristler', 'wristler');
        $this->method_description = __('Fill in your Wristler Security Token in order for the plugin to work.', 'wristler');

        $this->init_form_fields();
        $this->init_settings();

        add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
    }

    public function validate_wristler_security_token_field($key, $value)
    {
        $response = wp_remote_get('https://data.wristler.eu/api/v1', [
            'timeout' => 5,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $value,
            ],
        ]);

        if (wp_remote_retrieve_response_code($response) !== 200) {
            WC_Admin_Settings::add_error(__('The security token is invalid. Please check the token and try again.', 'wristler'));
        }

        return $value;
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'wristler_security_token' => array(
                'title' => __('Security token', 'wristler'),
                'type' => 'text',
                'description' => __('Enter the security token provided during installation on Wristler. Without this token the plug-in will not work.', 'wristler'),
                'desc_tip' => true,
                'default' => ''
            ),
        );
    }

}