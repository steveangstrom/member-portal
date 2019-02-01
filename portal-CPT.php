<?php
# nothing here yet
if ( ! defined( 'ABSPATH' ) ) { exit;} // Exit if accessed directly.

add_action( 'init', 'pheriche_portal_cpt', 0 );

function pheriche_portal_cpt() {
	register_post_type( 'portal-page',
		array( 'labels' => array(
			'name' => __( 'Member Portal', 'pherichetheme' ),
			'singular_name' => __( 'Portal Item', 'pherichetheme' ),
			'all_items' => __( 'All portal pages', 'pherichetheme' ),
			'add_new' => __( 'Add a Portal page', 'pherichetheme' ),
			'add_new_item' => __( 'Add page', 'pherichetheme' ),
			'edit' => __( 'Edit', 'pherichetheme' ),
			'edit_item' => __( 'Edit portal page', 'pherichetheme' ),
			'new_item' => __( 'New portal page', 'pherichetheme' ),
			'view_item' => __( 'View portal page', 'pherichetheme' ),
			'search_items' => __( 'Search portal pages', 'pherichetheme' ),
			'not_found' =>  __( 'Nothing found in the Database.', 'pherichetheme' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'pherichetheme' ),
			'parent_item_colon' => ''
			),
			'description' => __( 'The portal pages available in the system', 'pherichetheme' ),
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'show_in_vav_menus' =>true,
			'menu_position' => 5,
			'menu_icon'=> 'dashicons-format-gallery',
			'rewrite'	=> array( 'slug' => 'portal', 'with_front' => 'portal'),
			'has_archive' => false,
			'capability_type' => 'page',
			'hierarchical' => true,
			'show_in_rest'       => true,
			'template' => array(array( 'core/image', array('align' => 'left')),
            array( 'core/heading', array('placeholder' => 'Add Description')),
						array( 'core/paragraph', array('placeholder' => 'Add Description...')),
					),
			//'template_lock' => 'all', // or 'insert' to allow moving
			'rest_base'          => 'pher_portal',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports' => array( 'title', 'editor', 'thumbnail')
		)
	);
	register_taxonomy(
			'portalcategoryname',
			'portal-page',
			array(
				'label' => __( 'Portal Category' ),
				'rewrite' => array( 'slug' => 'genre' ),
				'hierarchical' => true,
				'show_in_rest'          => true,
				'rest_base'             => 'portal-category',
				'rest_controller_class' => 'WP_REST_Terms_Controller',
			)
		);
}
