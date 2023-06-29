<?php

namespace Wristler\Rest\Routes;

use WP_REST_Request as Request;
use Wristler\Rest\Route;
use Wristler\Wristler;

class Information extends Route
{

    public function __construct()
    {
        parent::__construct('information', $this->getMethods());
    }

    private function getMethods()
    {
        return [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'information'],
                'permission_callback' => [$this, 'middleware']
            ],
        ];
    }

    public function information(): array
    {
        return [
            'name' => get_bloginfo('name'),
            'versions' => [
                'wordpress' => get_bloginfo('version'),
                'php' => phpversion(),
                'woocommerce' => WC()->version,
                'plugin' => Wristler::VERSION,
            ],
            'limits' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'max_input_vars' => ini_get('max_input_vars'),
                'max_upload_size' => ini_get('upload_max_filesize'),
            ],
        ];

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
}