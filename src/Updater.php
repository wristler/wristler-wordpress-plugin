<?php

namespace Wristler;

class Updater
{

    public string $cacheKey = 'wristler_update_cache';

    public function __construct()
    {
        add_filter('plugins_api', array($this, 'info'), 10, 3);
        add_filter('site_transient_update_plugins', array($this, 'update'));
        add_action('upgrader_process_complete', array($this, 'purge'), 10, 2);
    }

    public function info($response, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $response;
        }

        if ($args->slug !== 'wristler/src') {
            return $response;
        }

        $response = $this->getRemotePluginInformation();

        if (!$response) {
            return $response;
        }

        return (object)[
            'name' => $response->name,
            'slug' => $response->slug,
            'version' => $response->version,
            'tested' => $response->tested,
            'requires' => $response->requires,
            'author' => $response->author,
            'author_profile' => $response->author_profile,
            'download_link' => $response->download_url,
            'trunk' => $response->download_url,
            'requires_php' => $response->requires_php,
            'last_updated' => $response->last_updated,
            'sections' => [
                'description' => $response->sections->description,
                'installation' => $response->sections->installation,
                'changelog' => $response->sections->changelog,
            ],
            'banners' => [
                'low' => $response->banners->low,
                'high' => $response->banners->high,
            ],
        ];
    }

    public function update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $response = $this->getRemotePluginInformation();

        if ($response && version_compare(Wristler::VERSION, $response->version, '<') && version_compare($response->requires, get_bloginfo('version'), '<=') && version_compare($response->requires_php, PHP_VERSION, '<')) {
            $res = (object)[
                'slug' => 'wristler/src',
                'plugin' => 'wristler/wristler.php',
                'new_version' => $response->version,
                'tested' => $response->tested,
                'package' => $response->download_url,
            ];

            $transient->response[$res->plugin] = $res;
        }

        return $transient;
    }

    public function purge($upgrader, $options): void
    {
        if ($options['action'] === 'update' && $options['type'] === 'plugin') {
            delete_transient($this->cacheKey);
        }
    }

    private function getRemotePluginInformation()
    {
        $remote = get_transient($this->cacheKey);

        if (!$remote) {
            $remote = wp_remote_get('https://raw.githubusercontent.com/wristler/wristler-wordpress-plugin/main/info.json', [
                'timeout' => 5,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            if (is_wp_error($remote) || wp_remote_retrieve_response_code($remote) !== 200 || empty(wp_remote_retrieve_body($remote))) {
                return false;
            }

            set_transient($this->cacheKey, $remote, HOUR_IN_SECONDS);
        }

        return json_decode(wp_remote_retrieve_body($remote));
    }
}