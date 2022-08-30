<?php
namespace WPUmbrella\Services\Provider;

if (!defined('ABSPATH')) {
    exit;
}

use Morphism\Morphism;
use WP_Query;

class WordPressData
{
    const NAME_SERVICE = 'WordPressDataProvider';

    public function countPosts()
    {
        global $wp_post_types;

        $args = [
            'show_ui' => true,
            'public' => true,
        ];

        $postTypes = get_post_types($args);
        unset(
            $postTypes['attachment'],
            $postTypes['seopress_rankings'],
            $postTypes['seopress_backlinks'],
            $postTypes['seopress_404'],
            $postTypes['elementor_library'],
            $postTypes['customer_discount'],
            $postTypes['cuar_private_file'],
            $postTypes['cuar_private_page'],
            $postTypes['ct_template']
        );
        $postTypes = apply_filters('wp_umbrella_count_posts', $postTypes);

        try {
            $args = [
                'post_type' => $postTypes,
                'post_status' => 'publish'
            ];
            $query = new WP_Query($args);
            return (int) $query->found_posts;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function countAttachments()
    {
        try {
            global $wpdb;

            $count = $wpdb->get_row("SELECT COUNT( * ) AS num_posts FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' ", ARRAY_A);
            if (!isset($count['num_posts'])) {
                return 0;
            }
            return (int) $count['num_posts'];
        } catch (\Exception $e) {
            return 0;
        }
    }
}
