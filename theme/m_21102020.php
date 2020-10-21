<?php
/**
 *
 */

namespace migrations\theme;


class page_home_21102020 extends \migrations\WP_Migration
{
    public function up()
    {
        global $wpdb;

        // delete revision
        $remove = get_posts([
            'numberposts' => -1,
            'post_type'   => 'page',
            'post_status' => 'inherit'
        ]);
        foreach ($remove as $post){
            wp_delete_post($post, true);
        }

        $posts = $wpdb->get_results("SELECT * FROM `rwp_posts` WHERE `post_title` LIKE '%home%'");

        if(is_array($posts)) {
            foreach ($posts as $post) {
                //$post = \WP_Post::get_instance($post->ID);
                $post->post_content = '';
                wp_insert_post((array)$post);
                update_post_meta($post->ID,'_wp_page_template', 'page-main.php');
                $this->delete_meta($post);

                $lang = pll_get_post_language($post->ID);
                switch ($lang){
                    case 'ru':
                        update_post_meta($post->ID, '_pagehome', serialize('156989, 153400, 151954, 150642, 144514, 143537, 139620'));
                        break;
                    case 'en':
                        update_post_meta($post->ID, '_pagehome', serialize('156988, 153390, 151937, 150650, 144526, 143554, 139689'));
                        break;
                    case 'pt':
                        update_post_meta($post->ID, '_pagehome', serialize('156999, 153431, 151964, 150686, 144557, 143620, 139939'));
                        break;
                    case 'es':
                        update_post_meta($post->ID, '_pagehome', serialize('157004, 153434, 152027, 150694, 144562, 143629, 139974'));
                        break;
                    case 'tr':
                        update_post_meta($post->ID, '_pagehome', serialize('139904, 130857, 121712, 119179, 27052'));
                        break;
                    case 'id':
                        update_post_meta($post->ID, '_pagehome',('157022, 153420, 152031, 150664, 144539, 143579, 139761, 130997'));
                        break;
                    case 'vi':
                        update_post_meta($post->ID, '_pagehome', serialize('157017, 153406, 152036, 150669, 144544, 143587, 139797, 130927'));
                        break;
                    case 'th':
                        update_post_meta($post->ID, '_pagehome', serialize('157012, 153425, 151960, 150674, 144548, 143600, 139833'));
                        break;
                    case 'ms':
                        update_post_meta($post->ID, '_pagehome', serialize('157031, 153415, 151945, 150658, 144535, 143566, 139726'));
                        break;
                    case 'ar':
                        update_post_meta($post->ID, '_pagehome', serialize('56818, 56262, 56858, 56872, 57338'));
                        break;
                    case 'fr':
                        update_post_meta($post->ID, '_pagehome', serialize('157008, 153428, 152040, 150756'));
                        break;
                    case 'zh':
                        update_post_meta($post->ID, '_pagehome', serialize('157036, 153437, 151967, 150679, 144552, 143610'));
                        break;
                }
            }
        }


        $wpdb->get_results(" DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = 'fw_options'");

        $wpdb->get_results("DROP TABLE ` rwp_td_terms `");
    }

    private function delete_meta($post){
        global $wpdb;

        $metas = $wpdb->get_results("SELECT * FROM `rwp_postmeta` WHERE `post_id` = {$post->ID} AND `meta_key` LIKE '%_oembed%'");
        foreach ($metas as $meta){
            $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta`.`meta_id` = {$meta->meta_id}");
        }

        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = 'fw_options' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_wpb_shortcodes_custom_css' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_yst_prominent_words_version' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_wpb_vc_js_status' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = 'utm_date' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = 'utm_geo' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = 'utm_geo' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_yoast_wpseo_content_score' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_yoast_wpseo_focuskeywords' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_yoast_wpseo_keywordsynonyms' AND `post_id` = {$post->ID}");
        $wpdb->get_results("DELETE FROM `rwp_postmeta` WHERE `rwp_postmeta` . `meta_key` = '_yoast_wpseo_meta-robots-noindex' AND `post_id` = {$post->ID}");
    }
}
new page_home_21102020();