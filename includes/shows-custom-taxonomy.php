<?php
/**
* Register Custom Taxonomy 'shows'
*/
function custom_register_shows_taxonomy() {
    $labels = array(
        'name'              => _x( 'Shows', 'taxonomy general name', 'video-shows' ),
        'singular_name'     => _x( 'Show', 'taxonomy singular name', 'video-shows' ),
        'search_items'      => __( 'Search Shows', 'video-shows' ),
        'all_items'         => __( 'All Shows', 'video-shows' ),
        'parent_item'       => __( 'Parent Show', 'video-shows' ),
        'parent_item_colon' => __( 'Parent Show:', 'video-shows' ),
        'edit_item'         => __( 'Edit Show', 'video-shows' ),
        'update_item'       => __( 'Update Show', 'video-shows' ),
        'add_new_item'      => __( 'Add New Show', 'video-shows' ),
        'new_item_name'     => __( 'New Show Name', 'video-shows' ),
        'menu_name'         => __( 'Shows', 'video-shows' ),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'shows' ),
        'show_in_rest'          => true, // Ensure REST API support

    );

    register_taxonomy( 'shows', 'video', $args );
}
add_action( 'init', 'custom_register_shows_taxonomy' );