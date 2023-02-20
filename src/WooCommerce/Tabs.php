<?php

namespace Wristler\WooCommerce;

class Tabs
{

    public $options;

    public function __construct()
    {
        $options = get_option('woocommerce_wristler_settings');

        if (!isset($options['wristler_security_token'])) {
            return;
        }

        $this->options = $options;

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

        echo '<input name="wristler_security_token" type="hidden" value="' . $this->options['wristler_security_token'] . '">';

        woocommerce_wp_checkbox(
            [
                'id' => '_wristler_sync',
                'value' => get_post_meta(get_the_ID(), '_wristler_sync', true) ? 'yes' : 'no',
                'wrapper_class' => 'show_if_simple',
                'label' => __('Synchronization', 'wristler'),
                'description' => __('Publish this watch on Wristler', 'wristler')
            ]
        );

        echo '<div class="wristler_fields">';
        woocommerce_wp_text_input(array(
            'id' => '_wristler_reference',
            'value' => get_post_meta(get_the_ID(), '_wristler_reference', true),
            'label' => __('Reference no.', 'wristler'),
            'wrapper_class' => 'is-loading',
        ));

        echo '<ul class="wristler-autocomplete-loader">
                <li class="is-loading"></li>
                <li class="is-loading"></li>
                <li class="is-loading"></li>
            </ul>';

        echo '<ul id="wristler-autocomplete-results"></ul>';

        echo '<div class="wristler-selected-watch-container"><div class="wristler-selected-watch"></div>';

        woocommerce_wp_text_input(array(
            'id' => '_wristler_selected_id',
            'value' => get_post_meta(get_the_ID(), '_wristler_selected_id', true),
            'label' => __('Selected watch', 'wristler'),
            'readonly' => true,
            'disabled' => true,
        ));

        echo '<span style="margin-top:30px;display: block;"></span>';

        woocommerce_wp_text_input(array(
            'id' => '_wristler_name',
            'value' => get_post_meta(get_the_ID(), '_wristler_name', true) ?: get_the_title(get_the_ID()),
            'label' => __('Name', 'wristler'),
        ));

        woocommerce_wp_text_input(array(
            'id' => '_wristler_price',
            'value' => get_post_meta(get_the_ID(), '_wristler_price', true) ?: get_post_meta(get_the_ID(), '_regular_price', true),
            'label' => __('Price', 'wristler'),
        ));

        woocommerce_wp_text_input(array(
            'id' => '_wristler_shipping_costs',
            'value' => get_post_meta(get_the_ID(), '_wristler_shipping_costs', true),
            'label' => __('Shipping costs', 'wristler'),
        ));

        woocommerce_wp_select([
            'id' => '_wristler_state',
            'label' => __('State', 'wristler'),
            'value' => get_post_meta(get_the_ID(), '_wristler_state', true),
            'options' => [
                'PRE_OWNED' => __('Pre-owned', 'wristler'),
                'NEW_UNWORN' => __('New/unworn', 'wristler'),
            ],
        ]);

        woocommerce_wp_select([
            'id' => '_wristler_condition',
            'label' => __('Condition', 'wristler'),
            'value' => get_post_meta(get_the_ID(), '_wristler_condition', true),
            'options' => [
                'FAIR' => __('Fair', 'wristler'),
                'GOOD' => __('Good', 'wristler'),
                'INCOMPLETE' => __('Incomplete', 'wristler'),
                'NEW' => __('New', 'wristler'),
                'POOR' => __('Poor', 'wristler'),
            ],
        ]);

        woocommerce_wp_select([
            'id' => '_wristler_availability',
            'label' => __('Availability', 'wristler'),
            'value' => get_post_meta(get_the_ID(), '_wristler_availability', true),
            'options' => [
                'ON_REQUEST' => __('On request', 'wristler'),
                'READY_TO_SHIP_IN_1_3_DAYS' => __('Ready to ship in 1-3 days', 'wristler'),
                'READY_TO_SHIP_IN_3_5_DAYS' => __('Ready to ship in 3-5 days', 'wristler'),
                'READY_TO_SHIP_IN_6_10_DAYS' => __('Ready to ship in 6-10 days', 'wristler'),
            ],
        ]);

        woocommerce_wp_text_input(array(
            'id' => '_wristler_year_of_production',
            'type' => 'number',
            'value' => get_post_meta(get_the_ID(), '_wristler_year_of_production', true),
            'label' => __('Year of production', 'wristler'),
        ));

        woocommerce_wp_checkbox(
            [
                'id' => '_wristler_box',
                'value' => get_post_meta(get_the_ID(), '_wristler_box', true) === 'yes' ? 'yes' : 'no',
                'label' => __('Box', 'wristler'),
                'description' => __('Includes original box', 'wristler')
            ]
        );

        woocommerce_wp_checkbox(
            [
                'id' => '_wristler_papers',
                'value' => get_post_meta(get_the_ID(), '_wristler_papers', true) === 'yes' ? 'yes' : 'no',
                'label' => __('Papers', 'wristler'),
                'description' => __('Includes papers/certificate', 'wristler')
            ]
        );

        woocommerce_wp_checkbox(
            [
                'id' => '_wristler_warranty',
                'value' => get_post_meta(get_the_ID(), '_wristler_warranty', true) === 'yes' ? 'yes' : 'no',
                'label' => __('Warranty', 'wristler'),
                'description' => __('Includes warranty', 'wristler')
            ]
        );

        woocommerce_wp_textarea_input(array(
            'id' => '_wristler_description',
            'label' => __('Description', 'wristler'),
            'value' => get_post_meta(get_the_ID(), 'c', true) ?: wp_strip_all_tags(get_the_content(get_the_ID())),
            'rows' => 5,
        ));

        woocommerce_wp_checkbox(
            [
                'id' => '_wristler_aftermarket',
                'value' => get_post_meta(get_the_ID(), '_wristler_aftermarket', true) ? 'yes' : 'no',
                'label' => __('Aftermarket', 'wristler'),
                'description' => __('This watch has after market customizations *', 'wristler'),
            ]
        );

        echo '<small style="    margin-left: 160px;margin-top: -10px;display: block;margin-bottom: 15px;">* ' . __('When checked, this watch will not be published directly. Fill in the customization options on Wristler for this watch to be published.') . '</small>';


        echo '</div>';
        echo '</div>';


        echo '</div>';
    }

}