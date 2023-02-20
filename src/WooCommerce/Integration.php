<?php

namespace Wristler\WooCommerce;

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