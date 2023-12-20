<?php

namespace Wristler\WooCommerce;

class Columns
{

    public function __construct()
    {
        add_filter('manage_edit-product_columns', [$this, 'add_column_to_products']);
        add_filter('manage_posts_custom_column', [$this, 'populate_status_column'], 10, 2);
    }

    public function add_column_to_products($columns)
    {
        return $this->array_insert_before('featured', $columns, 'wristler_status', __('Wristler'));
    }

    public function populate_status_column($column_name, $product_id)
    {
        $status = $this->getStatus(
            $this->determineStatus($product_id)
        );

        if ('wristler_status' === $column_name) {
            echo '<div style="display: flex; align-items: center;height:100%;"><span style="display: block;background:' . $status['color'] . ';border-radius: 12px;width:12px;height:12px;margin-right:5px;flex-shrink: 0;"></span> ' . $status['label'] . '</div>';
        }
    }

    private function determineStatus(\WC_Product|int $product): string
    {
        if (is_int($product)) {
            $product = wc_get_product($product);
        }

        if (!$product) {
            return 'inactive';
        }

        if (get_post_meta($product->get_id(), '_wristler_sync', true) !== 'yes') {
            return 'inactive';
        }

        if (empty(get_post_meta($product->get_id(), '_wristler_reference', true))) {
            return 'incomplete';
        }

        return 'active';
    }

    private function getStatus($status)
    {
        $statuses = [
            'inactive' => [
                'color' => '#a44',
                'label' => __('Inactive', 'wristler'),
            ],
            'incomplete' => [
                'color' => '#eaa600',
                'label' => __('Incomplete', 'wristler'),
            ],
            'active' => [
                'color' => '#7ad03a',
                'label' => __('Active', 'wristler'),
            ],
        ];

        return $statuses[$status];
    }

    private function array_insert_before($key, array &$array, $new_key, $new_value)
    {
        if (array_key_exists($key, $array)) {
            $new = array();
            foreach ($array as $k => $value) {
                if ($k === $key) {
                    $new[$new_key] = $new_value;
                }
                $new[$k] = $value;
            }
            return $new;
        }
        return FALSE;
    }


}
