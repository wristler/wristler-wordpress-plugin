<?php

namespace Wristler;

class WooCommerce
{
    public function __construct()
    {
        add_filter('woocommerce_product_data_tabs', [$this, 'addAdditionalTab']);
        add_filter('woocommerce_product_data_panels', [$this, 'renderAdditionalTab']);
    }

    public function addAdditionalTab($tabs)
    {
        $tabs['wristler'] = [
            'label' => __('Wristler', 'wristler'),
            'target' => 'wristler_product_data',
            'priority' => 80,
            'class' => ['show_if_simple'],
        ];

        return $tabs;
    }

    public function renderAdditionalTab()
    {
        // Note the 'id' attribute needs to match the 'target' parameter set above
        echo '<div id="wristler_product_data" class="panel woocommerce_options_panel">';

        woocommerce_wp_checkbox(
            [
                'id' => '_wristler_sync',
                'value' => 'no',
                'wrapper_class' => 'show_if_simple',
                'label' => __('Synchronization', 'wristler'),
                'description' => __('Publish this watch on Wristler', 'wristler')
            ]
        );

        echo '<div class="wristler_fields">';
        woocommerce_wp_text_input(array(
            'id' => '_wristler_reference',
            'label' => __('Reference no.', 'woocommerce'),
        ));
        woocommerce_wp_text_input(array(
            'id' => '_wristler_selected_id',
            'label' => __('Selected watch', 'woocommerce'),
            'readonly' => true,
            'disabled' => true,
        ));
        echo '</div>';

        echo '<ul id="wristler-autocomplete-results"></ul>';

        echo '</div>';
    }
}