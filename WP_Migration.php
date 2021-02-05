<?php
/**
 * Wordpress DB Migration
 *
 * @author: Aleksey Tikhomirov <alex@rwsite.ru>
 */

namespace migrations;


use function get_class;
use function get_option;
use function json_decode;
use function sprintf;
use function update_option;

abstract class WP_Migration implements WP_Migration_Interface
{
    /** @var string - your plugin prefix */
    protected $prefix;
    protected $option_name;
    /** @var string - Prefix + Class Name */
    protected $name;

    protected $messages = [];

    public $title;
    public $description;
    public static $action = 'admin_init';

    /**
     * WP_Migration constructor.
     * @param null $prefix
     */
    public function __construct($prefix = null)
    {
        $this->prefix = isset($prefix) ? $prefix : '';
        $this->name = $this->prefix . get_class($this);
        $this->option_name = $this->prefix . 'migration';
    }


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

    /**
     * Функция вызывается после завершения миграции
     */
    public function run()
    {
        $result = $this->get_migrations();
        if( ! in_array($this->name, $result ?? [], true) ){
            $this->up();
            $this->down();
            $result[] = $this->name;
            error_log(sprintf(__('Migration %s complete.','wp-migration'),  $this->name));
            update_option($this->option_name, json_encode($result, JSON_UNESCAPED_UNICODE), true);
        }

        if(defined( 'DOING_AJAX' )) {
            add_action('wp_ajax_' . str_replace('migrations' . '\\', '', $this->name .'_ajax'), [$this, 'ajax_handler']);
        }
    }

    /**
     * Gat all migrations from DB
     *
     * @return mixed
     */
    protected function get_migrations(){
        return json_decode(get_option($this->option_name,''), true);
    }

    /**
     *
     */
    public function ajax_handler(){
        global $wpdb;

        $this->up();
        $this->down();

        $this->messages[] = 'Request memory usage: ' . round( memory_get_peak_usage() / 1024 / 1024,  2) . ' MB';

        echo json_encode([
            'current' => 1,
            'all'     => 1,
            'message' => $this->messages
        ]);
        wp_die();
    }
}
