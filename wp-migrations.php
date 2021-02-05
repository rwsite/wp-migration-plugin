<?php
/**
 * Plugin Name: WP Migrations
 * Version:     1.0
 * Plugin URI:  http://rwsite.ru/
 * Description: Plugin for migrations DB
 * Author:      Aleksey Tikhomirov <alex@rwsite.ru>
 * Author URI:  https://rwsite.ru/
 *
 * Tags: migration, wp
 * Requires at least: 4.6
 * Tested up to: 5.6
 * Requires PHP: 7.2
 *
 * Text Domain: wp-migration
 * Domain Path: /languages/
 */

namespace migrations;


defined( 'ABSPATH' ) || exit;

if(class_exists('migrations/plugin')){
    return;
}

spl_autoload_register(function ($className){
    $prefix = 'migrations';
    if (0 === strpos($className, $prefix)) {
        $class= str_replace($prefix . '\\', '', $className);
        $path = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';
        if (file_exists($path)) {
            include_once $path;
        } else {
            $path = realpath(__DIR__) . DIRECTORY_SEPARATOR . strtr($className, '\\', DIRECTORY_SEPARATOR) . '.php';
            if(file_exists($path)){
                include_once $path;
            }
        }
    }
});

class plugin extends WP_Migration
{
    public $option_name;
    public $capability = 'manage_options';

    public $slug;
    /**@var array */
    public $migration_files = [];

    public function __construct($glob= false)
    {
        if($glob) {
            parent::__construct();
            $this->title = esc_html__('DB Migrations', 'wp-migration');
            $this->slug = 'migrations';
            $this->migration_files = $this->find_migrations();
            $this->run();

            add_action('admin_menu', [$this, 'menu_init']);
        }
    }

    public function menu_init(){
        add_submenu_page('index.php', $this->title, $this->title, 'update_core', $this->slug, [$this, 'get_view'], null);
    }

    /**
     * Get view
     */
    public function get_view(){
        $migrations = $this->get_migrations();
        require_once 'template/interface.php';
    }

    /**
     * @return array|false
     */
    private function find_migrations(){
        return apply_filters('migration_files', glob( __DIR__ . '/migrations/*.php' ));
    }

    /**
     * Run
     */
    public function run(){
        foreach ($this->migration_files as $file){
            $class = str_replace(realpath(__DIR__) . '/', '', $file);
            $class = str_replace('.php','', $class);
            $class = str_replace('/', '\\', $class);
            add_action( $class::$action, [new $class(), 'run'] );
        }
    }
}

add_action('plugins_loaded', function () {
    new plugin(true);
}, 1);