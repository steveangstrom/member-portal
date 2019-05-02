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
require_once 'includes/has-bought-membership.php';
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

#menu removed in favour of the Woocommerce login.
 add_filter( 'wp_nav_menu_items', 'memberlogin_menu_item', 10, 2 );

function memberlogin_menu_item ( $items, $args ) {
// to-do change theme_location to the option.
  if ( $args->theme_location == get_option('portal_menuname')) {
	  global $username;
		$slug = get_post_field( 'post_name', get_post());
		$menuclasses = 'menu-item portal-menu-item menu-item-object-page menu-item-has-children has_sub narrow';

//	$menuclasses ='menu-item menu-item-type-post_type menu-item-object-page portal-menu-item ';

		if ( is_user_logged_in() ) {

			$user_ID = get_current_user_id();
			$nice_name=    get_user_meta($user_ID, 'first_name', true);
			if( $slug =='member-portal'){ $menuclasses.='current_page_item active'; }

					// create a log-out dropdown for the logged in menu
					$submenu='<div style="height: 0px;" class="second"><div class="inner"><ul class="portal_dropdown">
						<li  class="menu-item menu-item-type-post_type menu-item-object-page "><a href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'">Logout '.$nice_name.'</a></li>
						<!--<li class="menu-item menu-item-type-post_type menu-item-object-page "><a href=""><i class="menu_icon blank fa"></i><span>JUST A Placeholder</span></a></li>-->
					</ul></div></div>';

			 $items .= '<li class="'.$menuclasses.'"><a href="'. get_home_url().'/'.get_option('portal_userpage_location').'"><span class="portalitem">Member Portal</span></a>'.$submenu.'</li>';

			} else {

					$submenu='<div style="height: 0px;" class="second"><div class="inner"><ul class="portal_dropdown">
						<li  class="menu-item menu-item-type-post_type menu-item-object-page "><a href="'.get_option('portal_login_location') .'">Sign In</a></li>
						<li class="menu-item menu-item-type-post_type menu-item-object-page "><a href="'. get_option('portal_register_location')  .'">Register</a></li>
					</ul></div></div>';

				if( $slug =='sign-in'){ $menuclasses.='current_page_item active'; }
				 $items .= '<li class="'.$menuclasses.'"><a href="'.get_home_url().'/'.get_option('portal_userpage_location').'">Member Portal</a>'.$submenu.'</li>';
		}
    }
	if ( $args->menu->slug == 'learning-portal-menu') {
		 $items .= get_personal_portal_items();
	}
	return $items;
}
