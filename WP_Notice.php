<?php
/**
 * Admin notice
 *
 * @author Aleksei Tikhomirov <alex@rwsite.ru>
 */

namespace migrations;

/**
 * Class WP_Notice
 */
final class WP_Notice
{
    public const UPDATE_NAG = 'update_nag';
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const INFO = 'info';
    public const SUCCESS = 'success';

    public static $id = 0;
    public static $instances;

    private $message;
    private $type;

    public function __construct(string $message, string $type)
    {
        self::$id++;
        $instance_data[self::$id] = $this;
        $this->message = $message;
        $this->type = $type;
        $this->add_action();
    }

    public function add_action(){
        add_action('admin_init', add_action('admin_notices', [$this, 'renderNotice']));
    }

    /**
     * Echo notice
     */
    public function renderNotice()
    {
        echo '<div id="notice_' . self::$id . '" class="notice notice-' . $this->type . ' is-dismissible"><p>' . $this->message . '</p></div>';
    }

    /**
     * @return int
     */
    public function get_id(){
        return self::$id;
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public static function find_by_id($id){
        return self::$instances[$id] ?? null;
    }

    /**
     * @param $id
     * @param $message
     */
    public static function add_message($id, $message){
        $instance = self::find_by_id($id);
        if(isset($instance)) {
            $instance->message = $message;
        }
    }
}