<?php

namespace Wristler\WooCommerce;

class Processor
{

    public function __construct()
    {
        add_action('woocommerce_process_product_meta_simple', [$this, 'processFields']);
    }

    public function processFields($postId)
    {
        update_post_meta($postId, '_wristler_box', isset($_POST['_wristler_box']) ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_papers', isset($_POST['_wristler_papers']) ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_warranty', isset($_POST['_wristler_warranty']) ? 'yes' : 'no');
        update_post_meta($postId, '_wristler_aftermarket', isset($_POST['_wristler_aftermarket']) ? 'yes' : 'no');

        foreach ($this->getFieldsFromRequest() as $key => $field) {
            update_post_meta($postId, $key, $field);
        }
    }

    protected function getFieldsFromRequest()
    {
        $keys = array_filter(array_keys($_POST), function ($key) {
            return substr($key, 0, 10) === '_wristler_' && !in_array($key, ['_wristler_box', '_wristler_papers', '_wristler_warranty', '_wristler_aftermarket']);
        });

        return array_filter($_POST, function ($v) use ($keys) {
            return in_array($v, $keys);
        }, ARRAY_FILTER_USE_KEY);

    }

}