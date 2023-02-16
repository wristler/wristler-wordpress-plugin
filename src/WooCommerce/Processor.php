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
        foreach ($this->getFieldsFromRequest() as $key => $field) {
            update_post_meta($postId, $key, $field);
        }
    }

    protected function getFieldsFromRequest()
    {
        $keys = array_filter(array_keys($_POST), function ($key) {
            return substr($key, 0, 10) === '_wristler_';
        });

        return array_filter($_POST, function ($v) use ($keys) {
            return in_array($v, $keys);
        }, ARRAY_FILTER_USE_KEY);

    }

}