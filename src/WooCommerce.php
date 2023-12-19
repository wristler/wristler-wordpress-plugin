<?php

namespace Wristler;

use Wristler\WooCommerce\Columns;
use Wristler\WooCommerce\Integration;
use Wristler\WooCommerce\Processor;
use Wristler\WooCommerce\Tabs;

class WooCommerce
{

    protected $tabs;

    protected $processor;

    protected $columns;

    public function __construct()
    {
        $this->tabs = new Tabs();
        $this->processor = new Processor();
        $this->columns = new Columns();

        add_filter('woocommerce_integrations', function ($integrations) {
            $integrations[] = new Integration();

            return $integrations;
        });

        add_action('woocommerce_product_duplicate', function ($product) {
            $meta = array_filter(get_post_meta($product->get_id()), function($key) {
                return str_starts_with($key, '_wristler_');
            }, ARRAY_FILTER_USE_KEY);

            $meta = array_keys($meta);

            foreach($meta as $key) {
                delete_post_meta($product->get_id(), $key);
            }
        }, 10);
    }


}