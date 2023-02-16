<?php

namespace Wristler;

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
    }


}