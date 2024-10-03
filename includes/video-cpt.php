<?php
/**
* Register Custom Post Type 'video'
*/
function custom_register_video_post_type() {
    $labels = array(
        'name'               => _x( 'Videos', 'post type general name', 'video-shows' ),
        'singular_name'      => _x( 'Video', 'post type singular name', 'video-shows' ),
        'menu_name'          => _x( 'Videos', 'admin menu', 'video-shows' ),
        'name_admin_bar'     => _x( 'Video', 'add new on admin bar', 'video-shows' ),
        'add_new'            => _x( 'Add New', 'video', 'video-shows' ),
        'add_new_item'       => __( 'Add New Video', 'video-shows' ),
        'new_item'           => __( 'New Video', 'video-shows' ),
        'edit_item'          => __( 'Edit Video', 'video-shows' ),
        'view_item'          => __( 'View Video', 'video-shows' ),
        'all_items'          => __( 'All Videos', 'video-shows' ),
        'search_items'       => __( 'Search Videos', 'video-shows' ),
        'parent_item_colon'  => __( 'Parent Videos:', 'video-shows' ),
        'not_found'          => __( 'No videos found.', 'video-shows' ),
        'not_found_in_trash' => __( 'No videos found in Trash.', 'video-shows' )
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'video-shows' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'video' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'menu_icon'           => 'dashicons-playlist-video',
        'show_in_rest'          => true, // Ensure REST API support

    );

    register_post_type( 'video', $args );
}
add_action( 'init', 'custom_register_video_post_type' );