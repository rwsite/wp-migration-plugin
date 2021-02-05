<?php

namespace migrations;

class AJAX_Example extends WP_Migration
{
    public $title = 'Fix links';
    public $description = 'Удаление "/platform" для id постов';

    /**
     * @return int[]|\WP_Post[]
     */
    public function get_all_posts(){
        $result = get_posts([
            'numberposts'   => -1,
            'post_type'     => 'post',
        ]);
        return $result;
    }

    public function up(){
        // miss automatic run
    }

    public function down(){}

    public function run()
    {
        parent::run();
    }

    /**
     * Run only for Ajax request
     */
    public function ajax_handler(){

        global $wpdb;
        $current = intval($_POST['data']['start']);
        $posts = $this->get_all_posts();
        foreach ($posts as $post) {
            $post->post_content = str_replace('something', 'something', $post->post_content);
            $wpdb->get_results($wpdb->prepare("UPDATE $wpdb->posts SET `post_content` = '%s' WHERE `$wpdb->posts`.`ID` = %d;", $post->post_content, $post->ID));
            $current++;
            // $message[] = 'Replace failed or success';
        }
        $message[] = 'Request memory usage: ' . round( memory_get_peak_usage() / 1024 / 1024,  2) . ' MB';

        echo json_encode([
            'current' => $current,
            'all'     => count($posts),
            'message' => isset($messages) ?  $messages : []
        ]);
        wp_die();
    }
}