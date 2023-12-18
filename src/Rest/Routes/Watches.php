<?php

namespace Wristler\Rest\Routes;

use WP_REST_Request as Request;
use Wristler\Rest\Route;

class Watches extends Route
{

    public function __construct()
    {
        parent::__construct('watches', $this->getMethods());
    }

    private function getMethods()
    {
        return [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'watches'],
                'permission_callback' => [$this, 'middleware']
            ],
        ];
    }

    public function watches(): array
    {
        return array_filter(array_map(function ($watch) {
            $meta = get_post_meta($watch->ID);

            $product = wc_get_product($watch);

            $name = !empty($meta['_wristler_name'][0])
                ? $meta['_wristler_name'][0]
                : get_the_title($watch->ID);

            $description = !empty($meta['_wristler_description'][0])
                ? $meta['_wristler_description'][0]
                : $watch->post_content;

            $priceOnRequest = empty($product->get_price());
            $price = 0;

            if(!$priceOnRequest) {
                $price = isset($meta['_wristler_sync_price'][0]) && $meta['_wristler_sync_price'][0] === 'yes'
                    ? round($product->get_price())
                    : round($meta['_wristler_price'][0]);
            }

            if (
                isset($meta['_wristler_price_on_request'][0]) && $meta['_wristler_price_on_request'][0] === 'yes' &&
                isset($meta['_wristler_sync_price'][0]) && $meta['_wristler_sync_price'][0] === 'no'
            ) {
                $priceOnRequest = true;
                $price = 0;
            }

            $shippingCosts = !empty($meta['_wristler_shipping_costs'][0]) && is_numeric($meta['_wristler_shipping_costs'][0])
                ? $meta['_wristler_shipping_costs'][0]
                : 0;

            $selectedReferenceUuid = isset($meta['_wristler_selected_id'][0]) && $meta['_wristler_selected_id'][0] !== 'unknown'
                ? $meta['_wristler_selected_id'][0]
                : null;

            $availability = $meta['_wristler_availability'][0] ?? null;

            if ($product->is_on_backorder()) {
                $availability = 'ON_REQUEST';
            }

            if(!$product->is_in_stock()) {
                return [];
            }

            $yearOfProduction = $meta['_wristler_year_of_production'][0] ?? null;

            if ($yearOfProduction && intval($yearOfProduction) <= 1900) {
                $yearOfProduction = 'BEFORE_1900';
            }

            if (empty($yearOfProduction) || ( isset($meta['_wristler_production_year_unknown'][0]) && $meta['_wristler_production_year_unknown'][0] === 'yes' )) {
                $yearOfProduction = 'UNKNOWN_RANDOM_SERIAL';
            }

            $images = $this->getImages($watch->ID);

            if (count($images) === 0) {
                return [];
            }

            return [
                'ID' => $watch->ID,
                'reference' => trim($meta['_wristler_reference'][0]) ?? null,
                'selectedReferenceUuid' => $selectedReferenceUuid,
                'name' => $name,
                'price' => $price,
                'priceOnRequest' => $priceOnRequest,
                'shippingCosts' => $shippingCosts,
                'state' => $meta['_wristler_state'][0] ?? null,
                'condition' => $meta['_wristler_condition'][0] ?? null,
                'availability' => $availability,
                'yearOfProduction' => $yearOfProduction,
                'box' => isset($meta['_wristler_box'][0]) && $meta['_wristler_box'][0] === 'yes',
                'papers' => isset($meta['_wristler_papers'][0]) && $meta['_wristler_papers'][0] === 'yes',
                'warranty' => isset($meta['_wristler_warranty'][0]) && $meta['_wristler_warranty'][0] === 'yes',
                'description' => preg_replace('#\[[^\]]+\]#', '', wp_strip_all_tags($description)),
                'aftermarket' => isset($meta['_wristler_aftermarket'][0]) && $meta['_wristler_aftermarket'][0] === 'yes',
                'images' => $this->getImages($watch->ID),
                'metadata' => [
                    'url' => get_permalink($watch->ID)
                ],
                'updatedAt' => get_the_modified_time('U', $watch->ID),
            ];
        }, $this->getWatches()->posts));
    }

    protected function getWatches()
    {
        return new \WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_wristler_sync',
                    'value' => 'yes',
                    'compare' => '='
                ],
                [
                    'key' => '_wristler_reference',
                    'compare' => '!=',
                    'value' => ''
                ],
                [
                    'relation' => 'OR',
                    [
                        'key' => '_manage_stock',
                        'compare' => '==',
                        'value' => 'no'
                    ],
                    [
                        'relation' => 'AND',
                        [
                            'key' => '_manage_stock',
                            'compare' => '==',
                            'value' => 'yes'
                        ],
                        [
                            'relation' => 'OR',
                            [
                                'key' => '_stock',
                                'compare' => '>=',
                                'value' => '1'
                            ],
                            [
                                'key' => '_backorders',
                                'compare' => '>=',
                                'value' => 'yes'
                            ],
                            [
                                'key' => '_backorders',
                                'compare' => '>=',
                                'value' => 'notify'
                            ],
                        ]
                    ],
                ]
            ],
        ]);
    }

    public function middleware(Request $request): bool
    {
        $options = get_option('woocommerce_wristler_settings');

        if (!isset($options['wristler_security_token'])) {
            return false;
        }

        if ($request->get_header('X-Wristler-Token') && $request->get_header('X-Wristler-Token') === $options['wristler_security_token']) {
            return true;
        }

        return false;
    }

    private function getImages($watchId): array
    {
        $watch = wc_get_product($watchId);

        $featuredImage = wp_get_attachment_image_src(get_post_thumbnail_id($watchId), 'full');
        $gallery = $watch->get_gallery_image_ids();

        if (!is_array($gallery)) {
            $gallery = [];
        }

        return array_filter([
            $featuredImage[0] ?? null,
            ...array_map(function ($image) {
                return wp_get_attachment_url($image);
            }, $gallery),
        ]);
    }
}