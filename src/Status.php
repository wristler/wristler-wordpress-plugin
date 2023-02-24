<?php

namespace Wristler;

class Status
{

    public static function hasConnection(): bool
    {
        $options = get_option('woocommerce_wristler_settings');

        if (!isset($options['wristler_security_token'])) {
            return false;
        }

        $response = wp_remote_get('https://data.wristler.eu/api/v1', [
            'timeout' => 5,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $options['wristler_security_token'],
            ],
        ]);

        return in_array(wp_remote_retrieve_response_code($response), [200, 201, 204]);
    }

}