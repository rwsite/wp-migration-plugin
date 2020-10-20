<?php
/**
 * Plugin Name: WP Migration plugin
 * Version:     0.1
 * Plugin URI:  http://rwsite.ru/
 * Description: WordPress simple migration Plugin
 * Author:      Aleksey Tikhomirov <alex@rwsite.ru>
 * Author URI:  https://rwsite.ru/
 *
 * Tags: migration, wp
 * Requires at least: 4.6
 * Tested up to: 5.5
 * Requires PHP: 7.2
 *
 * Text Domain: migration
 * Domain Path: /languages/
 */

namespace migrations;

spl_autoload_register(function ($className){
    $prefix = 'migrations';
    if (0 === strpos($className, $prefix)) {
        $className = str_replace($prefix . '\\', '',$className);
        $path = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
        if (file_exists($path)) {
            include_once $path;
        }
    }
});


class plugin extends WP_Migration
{
    public $option_name;
    public $capability = 'manage_options';
    public $title;

    public function __construct()
    {
        $this->title = __('Migrations', 'migration');
        parent::__construct();
    }

    /**
     * Run all migration
     *
     * @return array
     */
    public static function run(){
        foreach (glob(__DIR__ . '/*.php') as $file) {
            require_once $file;
        }
   }
}

add_action('plugins_loaded', function () {
    plugin::run();
    new plugin();
});