<?php
/*
Plugin Name:  Wristler
Plugin URI:   https://wristler.eu
Description:  This plug-ins allows your WooCommerce shop to sync with Wristler for automated stock updates.
Version:      1.0.0
Author:       Wristler
Author URI:   https://wristler.eu
Text Domain:  wristler
Domain Path:  /lang
Developer:    Jeffrey Ponsen <jeffrey@every-day.nl>
*/

namespace Wristler;

use Wristler\Rest\Rest;

if (!defined('WRISTLER_PLUGIN_PATH')) {
    define('WRISTLER_PLUGIN_PATH', dirname(__FILE__));
}

if (!defined('WRISTLER_RESOURCE_PATH')) {
    define('WRISTLER_RESOURCE_PATH', dirname(__FILE__) . '/resources');
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once(dirname(__FILE__) . '/vendor/autoload.php');
}

class Wristler
{

    const VERSION = '1.0.0';

    protected static $instance;

    protected $updater;

    protected $assets;

    protected $woocommerce;

    protected $rest;

    public static function get(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function __construct()
    {
        $this->updater = new Updater();
        $this->assets = new Assets();
        $this->woocommerce = new WooCommerce();
        $this->rest = new Rest();
    }

    public static function install() {

    }

}

function Wristler()
{
    return Wristler::get();
}

add_action('plugins_loaded', __NAMESPACE__ . '\\Wristler');

register_activation_hook(__FILE__, [Wristler::class, 'install']);


