<?php
/**
 * Plugin Name:       Video Shows
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       video-shows
 *
 * @package           create-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'VIDEO_SHOWS_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

 /**
 *  Register Post type and taxonomy
 */
include( VIDEO_SHOWS_DIR_PATH . '/includes/video-cpt.php');
include( VIDEO_SHOWS_DIR_PATH . '/includes/shows-custom-taxonomy.php');

/**
* Enqueue JavaScript and CSS for the block editor
*/
function latest_posts_block_enqueue_scripts() {
    wp_enqueue_script(
        'latest-posts-block-script',
        VIDEO_SHOWS_DIR_PATH . 'build/index.js',
        array('wp-blocks', 'wp-editor', 'wp-components', 'wp-api', 'wp-element')
    );
}
add_action('enqueue_block_editor_assets', 'latest_posts_block_enqueue_scripts');

function video_block_styling(){
	  wp_enqueue_style(
        'my-block-editor-style', // Style handle
        plugins_url( 'css/editor-style.css', __FILE__ ), // Style URL
        array( 'wp-edit-blocks' ), // Dependencies	
        filemtime( plugin_dir_path( __FILE__ ) . 'css/editor-style.css' ) // Version
    );
}
add_action('enqueue_block_editor_assets', 'video_block_styling');
add_action( 'enqueue_block_assets', 'video_block_styling' );

/**
* Register the block
*/
function video_shows_block_register_block() {
    register_block_type(
        __DIR__ . '/build' ,
        array(
            'editor_script' => 'latest-posts-block-script',
            'render_callback' => 'video_show_block_render',
            'attributes' => array(
                'postCount' => array(
                    'type' => 'number',
                    'default' => 3,
                ),
                'selectedShow' => array(
                    'type' =>  'string',
                    'default' => '', // Set default value here
                ),
                'selectedVideos' => array(
                    "type" =>  "array",
                    'default' => [], // Set default value here
                ),
            ),
        )
    );
}
add_action('init', 'video_shows_block_register_block');

/*
* Render callback function
*/
function video_show_block_render($attributes) {

    $args = array(
        'post_type' => 'video',
        'order'   => 'DESC',
        'orderby' => 'post_date',   
        'posts_per_page' => 3
    );

    // Check for a custom post count
    if ( isset( $attributes['postCount'] ) && $attributes['postCount'] !== '' ) {
        $args['posts_per_page'] = $attributes['postCount'];
    }

    // If specific videos are selected, filter by those videos
    if ( isset( $attributes['selectedVideos'][0] ) && $attributes['selectedVideos'][0] !== '' ) {
        $args['post__in'] = $attributes['selectedVideos'];
        $args['orderby']  = 'post__in';
        
        // Check if a specific show is selected
        if ( isset( $attributes['selectedShow'] ) && $attributes['selectedShow'] !== '' ) {
            $args['tax_query'] = array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
                array(
                    'taxonomy' => 'shows',
                    'field'    => 'term_id',
                    'terms'    => $attributes['selectedShow'],
                ),
            );
        }
    }

    $posts_query = new WP_Query($args);

    ob_start(); // Start output buffering

    if ($posts_query->have_posts()) {
        ?>
        <div class="video_shows_main">
        <?php
        while ($posts_query->have_posts()) {
            $posts_query->the_post();
            global $post;

            // Get the post thumbnail
            if (has_post_thumbnail($post->ID)) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                $vid_img_url = $image[0];
            } else {
                $vid_img_url = plugin_dir_url(__FILE__) . '/images/default-video-image.jpeg';	
            }

            // Output HTML for each video show
            ?>
            <div class="video_show">
                <h3><?php echo esc_html(get_the_title()); ?></h3>
                <img src="<?php echo esc_url($vid_img_url); ?>">
                <p>
                    <?php the_content(); ?>
                </p>
                <a href="<?php echo esc_url(get_the_permalink());?>">
                    <?php echo esc_html_e( 'Read More ', 'video-shows' );?>
                </a>
            </div>
            <?php
        }
        ?>
        </div>
        <?php
        wp_reset_postdata();
    } else {
        echo esc_html_e( 'No Video available.', 'video-shows' );
    }

    return ob_get_clean(); // Return the buffered content
}

