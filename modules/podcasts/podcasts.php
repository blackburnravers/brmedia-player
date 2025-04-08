<?php
/**
 * BRMedia Player Podcasts Module
 *
 * Registers the 'brpodcast' custom post type, defines the [brmedia_podcast] shortcode,
 * and generates an RSS feed for podcast episodes.
 *
 * @package BRMediaPlayer
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register the 'brpodcast' custom post type
 */
function brmedia_register_podcast_post_type() {
    $labels = array(
        'name'               => 'Podcasts',
        'singular_name'      => 'Podcast',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Podcast Episode',
        'edit_item'          => 'Edit Podcast Episode',
        'new_item'           => 'New Podcast Episode',
        'view_item'          => 'View Podcast Episode',
        'search_items'       => 'Search Podcast Episodes',
        'not_found'          => 'No podcast episodes found',
        'not_found_in_trash' => 'No podcast episodes found in Trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'taxonomies'         => array('brmedia_category', 'brmedia_tag'),
        'menu_icon'          => 'dashicons-microphone',
        'rewrite'            => array('slug' => 'podcasts'),
    );

    register_post_type('brpodcast', $args);
}
add_action('init', 'brmedia_register_podcast_post_type');

/**
 * Define the [brmedia_podcast] shortcode
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the podcast player
 */
function brmedia_podcast_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id'       => 0,
        'template' => 'default',
    ), $atts);

    $post_id = intval($atts['id']);
    if (!$post_id || get_post_type($post_id) !== 'brpodcast') {
        return '<p>Invalid podcast ID.</p>';
    }

    $audio_url = get_post_meta($post_id, 'brmedia_podcast_audio_url', true);
    if (!$audio_url) {
        return '<p>No podcast audio found.</p>';
    }

    $title = get_the_title($post_id);
    $template_path = BRMedia_Helpers::get_template_path('podcast', $atts['template']);
    if (!file_exists($template_path)) {
        return '<p>Podcast template not found.</p>';
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}
add_shortcode('brmedia_podcast', 'brmedia_podcast_shortcode');

/**
 * Add metabox for podcast audio
 */
function brmedia_podcast_metabox() {
    add_meta_box(
        'brmedia_podcast_audio',
        'Podcast Audio Details',
        'brmedia_podcast_audio_callback',
        'brpodcast',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'brmedia_podcast_metabox');

/**
 * Metabox callback
 *
 * @param WP_Post $post The current post object
 */
function brmedia_podcast_audio_callback($post) {
    wp_nonce_field('brmedia_podcast_audio_nonce', 'brmedia_podcast_audio_nonce');
    $audio_url = get_post_meta($post->ID, 'brmedia_podcast_audio_url', true);
    ?>
    <p>
        <label for="brmedia_podcast_audio_url">Podcast Audio URL:</label><br>
        <input type="url" name="brmedia_podcast_audio_url" id="brmedia_podcast_audio_url" value="<?php echo esc_attr($audio_url); ?>" style="width: 100%;" />
    </p>
    <?php
}

/**
 * Save podcast metabox data
 *
 * @param int $post_id The post ID
 */
function brmedia_save_podcast_audio($post_id) {
    if (!isset($_POST['brmedia_podcast_audio_nonce']) || !wp_verify_nonce($_POST['brmedia_podcast_audio_nonce'], 'brmedia_podcast_audio_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['brmedia_podcast_audio_url'])) {
        update_post_meta($post_id, 'brmedia_podcast_audio_url', esc_url_raw($_POST['brmedia_podcast_audio_url']));
    }
}
add_action('save_post', 'brmedia_save_podcast_audio');

/**
 * Generate RSS feed for podcasts
 */
function brmedia_podcast_rss_feed() {
    add_feed('brmedia_podcast', 'brmedia_podcast_rss_callback');
}
add_action('init', 'brmedia_podcast_rss_feed');

/**
 * RSS feed callback
 */
function brmedia_podcast_rss_callback() {
    header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?>';
    ?>
    <rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
        <channel>
            <title><?php bloginfo('name'); ?> Podcasts</title>
            <link><?php bloginfo('url'); ?></link>
            <description><?php bloginfo('description'); ?></description>
            <language>en-us</language>
            <?php
            $podcasts = new WP_Query(array('post_type' => 'brpodcast', 'posts_per_page' => 10));
            while ($podcasts->have_posts()) : $podcasts->the_post();
                $audio_url = get_post_meta(get_the_ID(), 'brmedia_podcast_audio_url', true);
                if ($audio_url) :
            ?>
                <item>
                    <title><?php the_title_rss(); ?></title>
                    <link><?php the_permalink_rss(); ?></link>
                    <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
                    <guid><?php the_guid(); ?></guid>
                    <description><![CDATA[<?php the_excerpt_rss(); ?>]]></description>
                    <enclosure url="<?php echo esc_url($audio_url); ?>" type="audio/mpeg"/>
                </item>
            <?php endif; endwhile; wp_reset_postdata(); ?>
        </channel>
    </rss>
    <?php
}