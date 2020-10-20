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
    public $prefix;
    public $option_name;
    protected $name;

    /**
     * WP_Migration constructor.
     * @param null $prefix
     */
    public function __construct($prefix = null)
    {
        $this->prefix = isset($prefix) ? $prefix : '';
        $this->name = $this->prefix . get_class($this);
        $this->option_name = $this->prefix . 'migration';

        $result = json_decode(get_option($this->option_name,''), true);
        if( ! in_array($this->name, $result ?? [], true) ){
            $this->up();
            $this->down();
            $result[] = $this->name;
            error_log(sprintf(__('Migration %s complete.','migration'),  $this->name));
            update_option($this->option_name, json_encode($result, JSON_UNESCAPED_UNICODE), 0);
        }
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
}
