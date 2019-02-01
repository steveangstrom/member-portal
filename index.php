<?php
/*
  Plugin Name: Member Portal
  Plugin URI: pheriche.com
  Author: Pheriche
  Version: 0.1
  Author URI:http://pheriche.com
 */

if ( ! defined( 'ABSPATH' ) ) { exit;} // Exit if accessed directly.
//error_reporting(E_ALL);

//include_once( 'includes/learning-portal-page.php' );// is a content apender// phased out in favour of template
	require_once 'includes/options-menu.php';

if ( file_exists( dirname( __FILE__ ) . '/includes/cmb2/init.php' ) ) {
	require_once 'includes/cmb2/init.php';
} else {
	add_action( 'admin_notices', 'cmb2_memberportal_plugin_missing_cmb2' );
}

require_once ('includes/CMB2-Grid/Cmb2GridPlugin.php');
require_once ('portal-CPT.php');

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
		//add_action( 'admin_head-post.php', 'legacyalert_acf_notice' ); // on an edit page go see if there are legacy files and links
		//add_action( 'admin_notices', 'legacyalert_acf_notice' );
}


function cmb2_memberportal_plugin_missing_cmb2() {
echo '<div class="error"><p>
Plugin is missing CMB2</p>
</div>';
 }


function legacyalert_acf_notice() {
// nag notice if they are editing a legacy page.
	$theID=get_the_ID();
	$protection = get_post_meta( $theID, 'resource_protection', 1 );

	$check_if_saved_since =  get_post_meta( $theID, 'resource_protection_override', 1 );
	$thelinks = get_post_meta( $theID, 'linkgroup', true );
	$files = get_post_meta($theID,'event_file_list', 1 );

	if ( gettype($thelinks)!='array' && gettype($thefiles) !='array'  ) return; // no links or files here so forget it.
	//if(isset($protection) && $protection == ''){$protection = 'public';	}
	if (  empty ( $check_if_saved_since) ){

  ?>
  <div class="update-nag notice">
     <?php echo( '<h3>NOTICE: Learning Portal Feature Update</h3> This page <b>'.get_the_title().'</b> contains legacy <em>Public</em> resources which are attached to the <em>Member</em> Resources section (red).  Currently site visitors will see these legacy documents as originally set(Public). <br> <b>Before saving please put any public resources into the dedicated Public Resources section</b>'); ?>
  </div>
  <?php

	}
  remove_action('admin_notices', 'legacyalert_acf_notice');
}


//* Redirect a user who isn't logged in AWAY from learning-portal -------- kinda important */

function pher_auth_redirect_user() {

	if( !is_user_logged_in () && is_page( array( 'learning-portal' )) ) {
		wp_redirect( home_url('/sign-in') ); exit;
	}
}
add_filter( 'pre_get_posts', 'pher_auth_redirect_user' );



  ################################################################################

/******  NEW template redirect for learning portal ********/

 function portal_single_template($single_template) {
  global $wp_query, $post;

	if ($post->post_name == "learning-portal" ){
		$single_template = dirname( __FILE__ ) . '/templates/learning_portal_template.php';
	}
    return $single_template;
}
//add_filter('page_template', 'portal_single_template'); //  SOON COME



 /**********   MENU ITEMS - Conditional adding, to avoid clash between "IF MENU" plugin and Qude plugin *******************/

add_filter( 'wp_nav_menu_items', 'your_custom_menu_item', 10, 2 );
function your_custom_menu_item ( $items, $args ) {

  if ( $args->theme_location == 'top-navigation') {
	  global $username;
		$slug = get_post_field( 'post_name', get_post());
		$menuclasses = 'menu-item portal-menu-item menu-item-object-page menu-item-has-children has_sub narrow';

//	$menuclasses ='menu-item menu-item-type-post_type menu-item-object-page portal-menu-item ';

		if ( is_user_logged_in() ) {

			$user_ID = get_current_user_id();
			$nice_name=    get_user_meta($user_ID, 'first_name', true);
			if( $slug =='learning-portal'){ $menuclasses.='current_page_item active'; }

					// create a log-out dropdown for the logged in menu
					$submenu='<div style="height: 0px;" class="second"><div class="inner"><ul class="portal_dropdown">
						<li  class="menu-item menu-item-type-post_type menu-item-object-page "><a href="'.wp_logout_url( home_url() ).'">Logout '.$nice_name.'</a></li>
						<!--<li class="menu-item menu-item-type-post_type menu-item-object-page "><a href=""><i class="menu_icon blank fa"></i><span>JUST A Placeholder</span></a></li>-->
					</ul></div></div>';

			 $items .= '<li class="'.$menuclasses.'"><a href="'.esc_url( home_url( '/learning-portal/' )).'"><span class="portalitem">Your Learning Portal</span></a>'.$submenu.'</li>';

			} else {

					$submenu='<div style="height: 0px;" class="second"><div class="inner"><ul class="portal_dropdown">
						<li  class="menu-item menu-item-type-post_type menu-item-object-page "><a href="'. home_url('sign-in') .'">Sign In</a></li>
						<li class="menu-item menu-item-type-post_type menu-item-object-page "><a href="'. home_url('register') .'">Register</a></li>
					</ul></div></div>';

				if( $slug =='sign-in'){ $menuclasses.='current_page_item active'; }
				 $items .= '<li class="'.$menuclasses.'"><a href="'.esc_url( home_url( '/sign-in/' )).'">The Learning Portal</a>'.$submenu.'</li>';
		}
    }
	if ( $args->menu->slug == 'learning-portal-menu') {
		 $items .= get_personal_portal_items();
	}
	return $items;
}


function get_personal_portal_items(){
	global $username;
	$out='';
	$out.=('<li><a href="'.wp_logout_url( home_url() ).'">Logout ('.$username.')</a></li>');
	$out.=('<li><a href="'.wp_lostpassword_url().'" title="Lost Password">Change Password</a></li>');
	return $out;
}

 /***** Content display for FILE OUTPUT and PROTECTED LINK ************/

add_filter( 'the_content', 'events_insert_downloads' );

function events_insert_downloads( $content ) {

	if ( is_single() || is_page()) { // added page cond 28-4-17
		// currently cmb2_getprotectedlink is not included
		if (function_exists('cmb2_getprotectedlink')) {
			$protectedlink = cmb2_getprotectedlink();
			$content.=$protectedlink;
		}
	}

	if ( is_single() || is_page()) { // added page cond
		// currently cmb2_output_file_list is not included
		if (function_exists('cmb2_output_file_list')) {
			$downloads = cmb2_output_file_list('event_file_list');// the id is a legacy.
			$content.=$downloads;
		}
	}

	return $content;
}
