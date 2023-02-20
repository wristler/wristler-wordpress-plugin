<?php

namespace Wristler;

use Wristler\WooCommerce\Integration;
use Wristler\WooCommerce\Processor;
use Wristler\WooCommerce\Tabs;

class WooCommerce
{

    protected $tabs;

    protected $processor;

    public function __construct()
    {
        $this->tabs = new Tabs();
        $this->processor = new Processor();

        add_filter('woocommerce_integrations', function ($integrations) {
            $integrations[] = new Integration();

            return $integrations;
        });
    }


}