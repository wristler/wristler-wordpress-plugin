<?php

namespace Wristler;

class Assets
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    public function enqueueAdminAssets()
    {
        wp_enqueue_style('wristler-admin-css', plugin_dir_url(WRISTLER_RESOURCE_PATH) . 'resources/css/admin.css', [], Wristler::VERSION);
        wp_register_script('wristler-admin-js', plugin_dir_url(WRISTLER_RESOURCE_PATH) . 'resources/js/admin.js', [], Wristler::VERSION);

        wp_localize_script('wristler-admin-js', 'WRISTLER', [
            'selectedWatch' => __('Selected watch', 'wristler'),
            'manuallyReferenced' => __('Manual reference', 'wristler'),
            'manuallyReferencedDescription' => __('Manually create this watch. Attributes should be filled at Wristler.', 'wristler'),
        ]);

        wp_enqueue_script('wristler-admin-js');
    }

}