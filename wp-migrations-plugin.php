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
        $path = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
        if (file_exists($path)) {
            include_once $path;
        }
    }
}, true, true);


class plugin extends WP_Migration
{
    public $capability = 'manage_options';
    public $title;

    public function __construct()
    {
        $this->title = __('Migrations', 'migration');
        parent::__construct();
        // add_action('admin_menu', [$this, 'register_sub_menu']);
    }

    /**
     * Register Submenu page
     */
    public function register_sub_menu(){
        add_submenu_page( 'index.php', $this->title, $this->title, $this->capability, $this->option_name, [$this, 'render_interface'] );
    }

    /**
     * Render html
     */
    public function render_interface(){
        echo '<div class="wrap">';
        echo '<h2>'. get_admin_page_title() .'</h2>';
        echo '</div>';
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