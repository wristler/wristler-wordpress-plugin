<?php

namespace Wristler\Rest;

abstract class Route extends \WP_REST_Controller
{

    protected $namespace = 'wristler';

    protected $version = '1';

    protected $rest_base = '';

    protected $methods;

    public function __construct($base, $methods)
    {
        $this->rest_base = $base;
        $this->methods = $methods;

        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes()
    {
        register_rest_route($this->namespace . '/v' . $this->version, '/' . $this->rest_base, $this->methods);
    }

}
