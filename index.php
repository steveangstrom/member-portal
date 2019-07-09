<?php
/*
  Plugin Name: Member Portal
  Plugin URI: pheriche.com
  Author: Pheriche
  Version: 0.51
  Author URI:http://pheriche.com
 */

if ( ! defined( 'ABSPATH' ) ) { exit;} // Exit if accessed directly.
//error_reporting(E_ALL);

//include_once( 'includes/learning-portal-page.php' );// is a content apender// phased out in favour of template
require_once 'includes/portal-options-menu.php';
//require_once 'includes/portal-utilities.php';
require_once 'includes/portal-components.php';
require_once 'includes/portal-blocks.php';
require_once 'includes/portal-userpage.php';
require_once ('portal-CPT.php');// include the custom post type


register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'memberportal_flush_rewrites' );
function memberportal_flush_rewrites() {
	// call your CPT registration function here (it should also be hooked into 'init')
//	memberportal_custom_post_types_registration();
	flush_rewrite_rules();
}

add_action( 'enqueue_block_editor_assets', 'portal_block_editor_styles' );
function portal_block_editor_styles() {
    wp_enqueue_style( 'portal-block-editor-styles', plugin_dir_url( __FILE__ ) . 'css/style-editor.css', false, '1.0', 'all' );
}

add_action( 'enqueue_scripts', 'pher_portal_frontendstyles', 0 );
function pher_portal_frontendstyles(){
	wp_enqueue_style( 'pher_portal_style', plugin_dir_url( __FILE__ ) . 'css/front-end-portal.css', array(), '0.1' );
}

add_action( 'admin_enqueue_scripts', 'pher_portal_adminstyles' );
function pher_portal_adminstyles() {
          wp_enqueue_style(  'pher_portal_adminstyle', plugin_dir_url( __FILE__ ) . 'css/admin-portal.css', array(), '0.1' );
}

//* Redirect a user who isn't logged in AWAY from the member-portal -------- kinda important! */

add_action( 'template_redirect', 'subscription_redirect_post' );
function subscription_redirect_post() {
  $queried_post_type = get_query_var('post_type');
  if (  !is_user_logged_in () && 'portal-page' ==  $queried_post_type ) {
    wp_redirect( home_url());
    exit;
  }
}

  ################################################################################

/******  NEW template redirect for learning portal ********/
/*
 function portal_single_template($single_template) {
  global $wp_query, $post;

	if ($post->post_name == "learning-portal" ){
		$single_template = dirname( __FILE__ ) . '/templates/learning_portal_template.php';
	}
    return $single_template;
}
*/
//add_filter('page_template', 'portal_single_template'); //  SOON COME

 /**********   MENU ITEMS - Conditional adding, to avoid clash between "IF MENU" plugin and Qude plugin *******************/
/**
 * Simple helper function for make menu item objects
 *
 * @param $title      - menu item title
 * @param $url        - menu item url
 * @param $order      - where the item should appear in the menu
 * @param int $parent - the item's parent item
 * @return \stdClass
 */
function _custom_nav_menu_item( $title, $url, $order, $parent = 0){
  $item = new stdClass();
  $item->ID = 1000000 + $order + $parent;
  $item->db_id = $item->ID;
  $item->title = $title;
  $item->url = $url;
  $item->menu_order = $order;
  $item->menu_item_parent = $parent;
  $item->type = '';
  $item->object = '';
  $item->object_id = '';
  $item->classes = array();
  $item->target = '';
  $item->attr_title = '';
  $item->description = '';
  $item->xfn = '';
  $item->status = '';
  return $item;
}


add_filter( 'wp_get_nav_menu_items', 'custom_nav_menu_member_portal', 20, 2 );
function custom_nav_menu_member_portal( $items, $menu ) {
	$top = _custom_nav_menu_item( 'Portal',  get_home_url().'/'.get_option('portal_userpage_location'), 100 );
  if ( $menu->slug == 'test' ) { // remember to get this from options not static !

	if(is_user_logged_in ()){ // if they are logged in then bother to query the page and slug for menu class hilite
		$postslug = get_post_field( 'post_name', get_post());
		if( $postslug =='member-portal'){ $menuclasses.='current_page_item active'; }
			$top->classes[]='current_page_item active';
	}
		$items[] = $top;

  if ( get_current_user_id() ){
	    $items[] = _custom_nav_menu_item( 'More Portal',  get_home_url().'/'.get_option('portal_userpage_location'), 101, $top->ID );
	    $items[] = _custom_nav_menu_item( 'Account Details',  get_permalink( woocommerce_get_page_id( 'myaccount' ) ), 103, $top->ID );
	    $items[] = _custom_nav_menu_item( 'Sign Out',  wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) , 102, $top->ID );
	  }else{
			 $items[] = _custom_nav_menu_item( 'Sign in / Register',  get_home_url().'/'.get_option('portal_login_location'), 101, $top->ID );
		}
  }
  return $items;
}


function portal_login_redirect( $redirect ) {
	$redirect_page_id = url_to_postid( $redirect );
	$checkout_page_id = wc_get_page_id( 'checkout' );

	if( $redirect_page_id == $checkout_page_id ) {
		return $redirect;
	}
	return get_home_url().'/'.get_option('portal_userpage_location');
}
add_filter( 'woocommerce_login_redirect', 'portal_login_redirect' );


function portal_register_redirect( $redirect ) {
    return wc_get_page_permalink( 'shop' );
}
add_filter( 'woocommerce_registration_redirect', 'portal_register_redirect' );
