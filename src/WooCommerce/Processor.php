<?php

namespace Wristler\WooCommerce;

class Processor
{

    public function __construct()
    {
        add_action('woocommerce_process_product_meta_simple', [$this, 'processFields']);
        add_action('woocommerce_process_product_meta_external', [$this, 'processFields']);
    }

    public function processFields($postId)
    {
        update_post_meta($postId, '_wristler_sync', isset($_POST['_wristler_sync']) && $_POST['_wristler_sync'] === 'yes'  ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_sync_price', isset($_POST['_wristler_sync_price']) && $_POST['_wristler_sync_price'] === 'yes'  ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_price_on_request', isset($_POST['_wristler_price_on_request']) && $_POST['_wristler_price_on_request'] === 'yes'  ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_box', isset($_POST['_wristler_box']) && $_POST['_wristler_box'] === 'yes'  ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_papers', isset($_POST['_wristler_papers']) && $_POST['_wristler_papers'] === 'yes'  ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_warranty', isset($_POST['_wristler_warranty']) && $_POST['_wristler_warranty'] === 'yes'  ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_aftermarket', isset($_POST['_wristler_aftermarket']) && $_POST['_wristler_aftermarket'] === 'yes' ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_production_year_unknown', isset($_POST['_wristler_production_year_unknown']) && $_POST['_wristler_production_year_unknown'] === 'yes' ? 'yes' : 'no');

        foreach ($this->getFieldsFromRequest() as $key => $field) {
            update_post_meta($postId, $key, $field);
        }
    }

    protected function getFieldsFromRequest()
    {
        $keys = array_filter(array_keys($_POST), function ($key) {
            return substr($key, 0, 10) === '_wristler_' && !in_array($key, ['_wristler_sync', '_wristler_sync_price', '_wristler_price_on_request', '_wristler_box', '_wristler_papers', '_wristler_warranty', '_wristler_aftermarket', '_wristler_production_year_unknown']);
        });

        return array_filter($_POST, function ($v) use ($keys) {
            return in_array($v, $keys);
        }, ARRAY_FILTER_USE_KEY);

    }

}