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
            'wristler_force_default_description' => array(
                'title' => __('Force default description', 'wristler'),
                'type' => 'checkbox',
                'label' => __('Force usage of default description (defaulted to watch name)', 'wristler'),
                'description' => __('When enabled, the description entered in the product settings will be ignored. The product name will be used as description.', 'wristler'),
                'desc_tip' => true,
            ),
            'wristler_spacer' => [
                'title' => __('Default attributes', 'wristler'),
                'type' => 'hidden',
            ],
            'wristler_attribute_state' => array(
                'title' => __('State', 'wristler'),
                'type' => 'select',
                'options' => [
                    'NEW_UNWORN' => __('New/unworn', 'wristler'),
                    'PRE_OWNED' => __('Pre-owned', 'wristler'),
                ],
                'default' => 'NEW_UNWORN',
            ),
            'wristler_attribute_condition' => array(
                'title' => __('Condition', 'wristler'),
                'type' => 'select',
                'options' => [
                    'NEW' => __('New', 'wristler'),
                    'UNWORN' => __('Unworn', 'wristler'),
                    'VERY_GOOD' => __('Very good', 'wristler'),
                    'GOOD' => __('Good', 'wristler'),
                    'FAIR' => __('Fair', 'wristler'),
                    'POOR' => __('Poor', 'wristler'),
                    'INCOMPLETE' => __('Incomplete', 'wristler'),
                ],
                'default' => 'NEW',
            ),
            'wristler_attribute_availability' => array(
                'title' => __('Availability', 'wristler'),
                'type' => 'select',
                'options' => [
                    'READY_TO_SHIP_IN_1_3_DAYS' => __('Ready to ship in 1-3 days', 'wristler'),
                    'READY_TO_SHIP_IN_3_5_DAYS' => __('Ready to ship in 3-5 days', 'wristler'),
                    'READY_TO_SHIP_IN_6_10_DAYS' => __('Ready to ship in 6-10 days', 'wristler'),
                    'ON_REQUEST' => __('On request', 'wristler'),
                ],
                'default' => 'READY_TO_SHIP_IN_1_3_DAYS',
            ),

            'wristler_attribute_shipping_costs' => array(
                'title' => __('Shipping costs', 'wristler'),
                'type' => 'number',
            ),
            'wristler_attribute_includes_box' => array(
                'title' => __('Including box', 'wristler'),
                'type' => 'select',
                'options' => [
                    'no' => __('No', 'wristler'),
                    'yes' => __('Yes', 'wristler'),
                ],
                'default' => 'no',
            ),
            'wristler_attribute_includes_papers' => array(
                'title' => __('Including papers', 'wristler'),
                'type' => 'select',
                'options' => [
                    'no' => __('No', 'wristler'),
                    'yes' => __('Yes', 'wristler'),
                ],
                'default' => 'no',
            ),
        );
    }

}