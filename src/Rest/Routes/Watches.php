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
        return array_map(function ($watch) {
            $meta = get_post_meta($watch->ID);

            return [
                'ID' => $watch->ID,
                'reference' => $meta['_wristler_reference'][0] ?? null,
                'selectedReferenceUuid' => $meta['_wristler_selected_id'][0] ?? null,
                'name' => $meta['_wristler_name'][0] ?? null,
                'price' => $meta['_wristler_price'][0] ?? null,
                'shippingCosts' => $meta['_wristler_shipping_costs'][0] ?? null,
                'state' => $meta['_wristler_state'][0] ?? null,
                'condition' => $meta['_wristler_condition'][0] ?? null,
                'availability' => $meta['_wristler_availability'][0] ?? null,
                'yearOfProduction' => $meta['_wristler_year_of_production'][0] ?? null,
                'box' => isset($meta['_wristler_box'][0]) && $meta['_wristler_box'][0] === 'yes',
                'papers' => isset($meta['_wristler_papers'][0]) && $meta['_wristler_papers'][0] === 'yes',
                'warranty' => isset($meta['_wristler_warranty'][0]) && $meta['_wristler_warranty'][0] === 'yes',
                'description' => $meta['_wristler_description'][0] ?? null,
                'aftermarket' => isset($meta['_wristler_aftermarket'][0]) && $meta['_wristler_aftermarket'][0] === 'yes',
                'images' => $this->getImages($watch->ID),
                'updatedAt' => get_the_modified_time('U', $watch->ID),
            ];
        }, $this->getWatches()->posts);
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
                    'key' => '_wristler_selected_id',
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
                            'key' => '_stock',
                            'compare' => '>=',
                            'value' => '1'
                        ],
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

        return [
            $featuredImage[0],
            ...array_map(function ($image) {
                return wp_get_attachment_url($image);
            }, $watch->get_gallery_image_ids()),
        ];
    }
}