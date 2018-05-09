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

if ( file_exists( dirname( __FILE__ ) . '/includes/cmb2/init.php' ) ) {
	require_once 'includes/cmb2/init.php';
} else {
	add_action( 'admin_notices', 'cmb2_memberportal_plugin_missing_cmb2' );
}

require_once ('includes/Cmb2Grid/Cmb2GridPlugin.php');



add_action( 'init', 'pher_portal_init', 0 );
function pher_portal_init(){
	wp_enqueue_style( 'pher_portal_style', plugin_dir_url( __FILE__ ) . 'css/front-end-portal.css', array(), '0.1' );

}

add_action( 'admin_enqueue_scripts', 'pher_portal_adminstyles' );
function pher_portal_adminstyles() {
        wp_register_style(  'pher_portal_adminstyle', plugin_dir_url( __FILE__ ) . 'css/admin-portal.css', array(), '0.1' );
        wp_enqueue_style( 'pher_portal_adminstyle' );

		add_action( 'admin_head-post.php', 'legacyalert_acf_notice' ); // on an edit page go see if there are legacy files and links
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

 ################################################################################

add_action( 'cmb2_admin_init', 'cmb2_learningportal_metaboxes' );

function cmb2_learningportal_metaboxes() {

$cmb_files = new_cmb2_box( array(
		'id'            => 'member_resources',
		'title'         => __( 'Member Resources', 'cmb2' ),
		'context'       => 'normal',
		'priority'      => 'high',
		'object_types'  => array( 'events','page','post'), // Post type
		'show_names'    => true, // Show field names on the left
) );

$public_files = new_cmb2_box( array(
		'id'            => 'public_resources',
		'title'         => __( 'Public Resources', 'cmb2' ),
		'context'       => 'normal',
		'priority'      => 'high',
		'object_types'  => array( 'events','page','post'), // Post type
		'show_names'    => true, // Show field names on the left
) );


$cmb_files->add_field( array(
		'name' => 'File List',
		'desc' => 'add files for this item',
		'id'   => 'event_file_list',
		'type' => 'file_list',
		'options' => array(
			'add_upload_files_text' => 'Add or Upload Files', // default: "Add or Upload Files"
			'remove_image_text' => 'Remove Image', // default: "Remove Image"
			'file_text' => 'File:', // default: "File:"
			'file_download_text' => 'Download', // default: "Download"
			'remove_text' => 'Remove', // default: "Remove"
		),
) );

$public_files->add_field( array(
		'name' => 'File List',
		'desc' => 'add files for this item',
		'id'   => 'public_file_list',
		'type' => 'file_list',
		'options' => array(
			'add_upload_files_text' => 'Add or Upload Files', // default: "Add or Upload Files"
			'remove_image_text' => 'Remove Image', // default: "Remove Image"
			'file_text' => 'File:', // default: "File:"
			'file_download_text' => 'Download', // default: "Download"
			'remove_text' => 'Remove', // default: "Remove"
		),
) );


/*	https://github.com/origgami/CMB2-grid/wiki/Group-fields	 */

	$group_field_id  = $cmb_files->add_field(array(
                'id'         => 'linkgroup',
                'type'       => 'group',
                'options'    => array(
                    'group_title'    => __('Link {#}', 'cmb2'), // {#} gets replaced by row number
                    'add_button'     => __('Add Another Link', 'cmb2'),
                    'remove_button'  => __('Remove Link', 'cmb2'),
                    'sortable'       => true,
                ),
     ));

	$gfield1=	$cmb_files->add_group_field('linkgroup', array(
		'name' => 'Protected Link',
		'desc' => 'If you need to protect a link, paste it into this box',
		'id'   => 'protectable_link',
		'type' => 'text_url',
		    'attributes'  => array(
       		 'placeholder' => '',)
	) );

	$gfield2=	$cmb_files->add_group_field('linkgroup', array(
		'name' => 'Link Text',
		'desc' => '(optional) Title for the protected link',
		'id'   => 'protectable_link_text',
		'type' => 'text',

	) );


	  //Create a default grid

	$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid($cmb_files);
	$cmb2GroupGrid   = $cmb2Grid->addCmb2GroupGrid('linkgroup');
	$row             = $cmb2GroupGrid->addRow();
	$row->addColumns(array(  $gfield1, $gfield2 ));
	$row = $cmb2Grid->addRow();
	$row->addColumns(array(
		$cmb2GroupGrid // Can be $group_field_id also
	));

# now the public link grid *

	$publicgroup_field_id  = $public_files->add_field(array(
                'id'         => 'publiclinkgroup',
                'type'       => 'group',
                'options'    => array(
                    'group_title'    => __('Link {#}', 'cmb2'), // {#} gets replaced by row number
                    'add_button'     => __('Add Another Link', 'cmb2'),
                    'remove_button'  => __('Remove Link', 'cmb2'),
                    'sortable'       => true,
                ),
     ));

	$pub_gfield1=	$public_files->add_group_field('publiclinkgroup', array(
		'name' => 'Public Link',
		'desc' => 'paste public links into this box',
		'id'   => 'public_link',
		'type' => 'text_url',
		    'attributes'  => array(
       		 'placeholder' => '',)
	));

	$pub_gfield2=	$public_files->add_group_field('publiclinkgroup', array(
		'name' => 'Link Text',
		'desc' => '(optional) Title for the public link',
		'id'   => 'public_link_text',
		'type' => 'text',

	));

	$cmb2Grid_public = new \Cmb2Grid\Grid\Cmb2Grid($public_files);
	$cmb2GroupGrid_public   = $cmb2Grid_public->addCmb2GroupGrid('publiclinkgroup');
	$row_public          = $cmb2GroupGrid_public->addRow();
	$row_public->addColumns(array(  $pub_gfield1, $pub_gfield2 ));
	$row_public = $cmb2Grid_public->addRow();
	$row_public->addColumns(array(
		$cmb2GroupGrid_public
	));



// taken out Dec 1 2016, as protection is now handled by putting protected resources into their own metabox
/*
$cmb_files->add_field( array(
    'name'    => 'Protect These Resources',
    'id'      => 'resource_protection',
    'type'    => 'radio_inline',
    'options' => array(
        'protected' => __( 'Members Only', 'cmb2' ),
        'public'   => __( 'Public Access', 'cmb2' ),
		),
	) );
*/
$cmb_files->add_field( array( // for my own debug purposes
    'name'    => 'Protect These Resources',
    'id'      => 'resource_protection',
     'type'    => 'hidden'
	) );

$cmb_files->add_field( array( // if a resource protection was previously set,  check this var to see if it's been resaved since, in which case its now a member file.
    'name'    => 'override_old_protection',
    'id'      => 'resource_protection_override',
    'type'    => 'hidden',
    'value' => 'protected',
	'default' => 'protected'
	) );


}



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
		$protectedlink = cmb2_getprotectedlink();
		$content.=$protectedlink;
	}

	if ( is_single() || is_page()) { // added page cond
		$downloads = cmb2_output_file_list('event_file_list');// the id is a legacy.
		$content.=$downloads;
	}

	return $content;
}



/**** protected link display ********/

function cmb2_getprotectedlink(){

	$theID=get_the_ID();
		$link ='';
	$thelinks = get_post_meta( $theID, 'linkgroup', true );

	$thepubliclinks = get_post_meta( $theID, 'publiclinkgroup', true );

	if (empty($thelinks[0])&& empty($thepubliclinks[0])){return ;} // nothing here, so dont bother

	$link .= '<div class="link-list-wrap"><h4>Links</h4>';

	//$protection = get_post_meta( $theID, 'resource_protection', 1 );

	$protection = get_post_meta( $theID, 'resource_protection', 1 ); // kept for potential reinclusion depending on client thoughts
	$check_if_saved_since =  get_post_meta( $theID, 'resource_protection_override', 1 );

	if(isset($protection) && $protection == ''){
		$protection = 'public';
		}

	if(isset($protection) && $protection != 'public'){
		$protection = 'protected'; // protection hard set for unsassigned meta on 1dec 2016, because public and private files are now in separate metaboxes
	}

	if(isset($check_if_saved_since)&& ($check_if_saved_since=='protected')){
		$protection = 'protected'; // protection hard set for items resaved since dec 2016
	}



	//$protection = 'protected'; // protection hard set on Dec 1 2016, because protected items are in their own metabox

	$user_status = is_user_logged_in ();
	$hasaccess = false;
	$class="locked";

		if (!empty($thelinks[0])){
			foreach ( (array) $thelinks as $key => $entry ) {
				$thelabel = $entry['protectable_link_text'];
					if(empty($thelabel)){$label='Visit Protected link ';}else{
					$label =$thelabel;
				}

				if(($protection=='protected' && $user_status==1) || empty($protection) || $protection=='public'){
					$hasaccess = true;
					$class="unlocked";
				}

				if($hasaccess == true){
					$linkurl = $entry['protectable_link'];
				}else{
					$linkurl=home_url('/sign-in'); // new
					$usermessage='<div class="usermessage">Member-only resource. Join for Access</div>';
				}
				if(!empty ($entry['protectable_link'])){	$link .= '<a href="'.$linkurl.'" class="protected-link '.$class.'" target="_blank">'.$label.$usermessage.'</a>';}


			}
		}// empty protected check

	/******* NOW LETS DO ANY PUBLIC LINKS IN THE SYSTEM.********/

	if (!empty($thepubliclinks[0])){
		foreach ( (array) $thepubliclinks as $key => $entry ) {

			$thelabel = $entry['public_link_text'];
				if(empty($thelabel)){$label='Visit link ';}else{
				$label =$thelabel;
			}

			$linkurl = $entry['public_link'];
				if(empty($linkurl)){$linkurl ='';}else{
				$linkurl=$entry['public_link'];
				}
			if (!is_user_logged_in()){	$class="unlocked public";	}else{$class="unlocked";}

			if(!empty ($entry['public_link'])){
				$link .= '<a href="'.$linkurl.'" class="protected-link '.$class.'" target="_blank">'.$label.'</a>';
			}


		}

	}
	$link.='</div>';

	// empty public links check

		/*echo('<pre>');
		print_r($thepubliclinks);
		echo('</pre>');*/


	return $link;

}

/*******file list display *****/

function cmb2_output_file_list( $file_list_meta_key, $img_size = 'medium' ) {

    // Get the list of files
	$theID=get_the_ID();
    $files = get_post_meta($theID, $file_list_meta_key, 1 );
	$publicfiles = get_post_meta($theID, 'public_file_list', 1 );


	if (empty($files)&&empty($publicfiles)){return ;} // IF nothing here, so dont bother

	$protection = get_post_meta( $theID, 'resource_protection', 1 ); // kept for potential reinclusion depending on client thoughts
	$check_if_saved_since =  get_post_meta( $theID, 'resource_protection_override', 1 );

	if(isset($protection) && $protection == ''){
		$protection = 'public'; // some legacy items have an empty value, so assume public.
	}
	if(isset($protection) && $protection != 'public'){
		$protection = 'protected'; // protection hard set for unsassigned meta on 1dec 2016, because public and private files are now in separate metaboxes
		}

	if(isset($check_if_saved_since)&& ($check_if_saved_since=='protected')){
		$protection = 'protected'; // protection hard set for items resaved since dec 2016
	}



	$user_status = is_user_logged_in ();
	$hasaccess = false;
	$class="locked";

	if(($protection=='protected' && $user_status==1) || empty($protection) || $protection=='public'){
		$hasaccess = true;
		$class="unlocked";
	}
		$out= '<div class="file-list-wrap"><h4>Downloads</h4>';

		// Loop through the Memberfiles and output
		foreach ((array) $files as $attachment_id => $attachment_url ) {
			$attachment_data = wp_prepare_attachment_for_js($attachment_id );

			if (!empty($files)){
				if($hasaccess == true){
					$dload_nonce=wp_create_nonce('download'.$attachment_id );
					#	$out.= '<a class="file-list '.$class.'" href="'.wp_get_attachment_url($attachment_id).'" target="blank">';//UNPROTECTED DIRECT LINK
					$out.= '<a class="file-list '.$class.'" href="?lpf='.$attachment_id.'&_wpnonce='.$dload_nonce.'">';
						$usermessage='';
				}else{
						$linkurl=home_url('/sign-in'); // new
						$out.= '<a class="file-list '.$class.'" href="'.$linkurl.'">';
						$usermessage='<div class="usermessage">Member-only resource. Join for Access</div>';
				}

				$out.=get_the_title($attachment_id);

				$out.=' ' .$attachment_data['subtype']. ' '. $attachment_data['filesizeHumanReadable'];
				$out.=$usermessage;
				$out.= '</a>';
			}
		}

	/******* NOW LETS DO ANY PUBLIC DOWNLOADS  IN THE SYSTEM.********/

		foreach ((array) $publicfiles as $attachment_id => $attachment_url ) {
			if (!is_user_logged_in() ){	$class="unlocked public";	}else{$class="unlocked";}

			if(!empty($attachment_url)){
				$out.= '<a class="file-list '.$class.'" href="'.$attachment_url.'">';
				$out.=get_the_title($attachment_id);

				$out.($attachment_id);
				$out.= '</a>';
			}
		}

    $out.='</div>';

	return $out;
}

 /********* log hook ***********/

add_action('pher_resdown','pher_resdown_log_trigger',10,3);

function pher_resdown_log_trigger($user_id , $status){
	return 'a returned var';
}

 /********** Librarian *****************/

add_action( 'init', 'lpf_librarian', 15 );
function lpf_librarian(){
	if(!isset($_REQUEST['lpf'])){return 0;}// abandon this function if there's no download request

	// theres a download requested ?
	$attachment_id=isset($_REQUEST['lpf'])?$_REQUEST['lpf']:null;

	//force download and obscure the real download link of a protected library file
    $mime=get_post_mime_type( $attachment_id);
	if (empty($mime)){$mime='application/octet-stream';}//or 'application/force-download'
	$filepath=get_attached_file( $attachment_id);
	$filename = basename($filepath);
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {$filename = preg_replace('/\./', '%2e', $filename, substr_count($filename, '.') - 1);}
		// workaround for IE filename bug with multiple periods / multiple dots in filename ??is this even still a bug??
	$filesize=@filesize( $filepath);
	 clearstatcache();

	//TO DO user activity   log the download and user id
	//do some checking - download nonce and is  user logged in?

	$user_status = is_user_logged_in ();
	$protection = get_post_meta( $attachment_id, 'resource_protection', 1 );

	if(($protection=='protected' && $user_status==1) || empty($protection) || $protection=='public'){
		$hasaccess = true;
	}

	$nonce = isset($_REQUEST['_wpnonce'])?$_REQUEST['_wpnonce']:'';
	if (wp_verify_nonce( $nonce, 'download'.$attachment_id  ) ) {

	$userID = get_current_user_id();
	do_action('pher_resdown',$userID, $filename );// steves PLAINVIEW download watching new hook, defined in phua_logging / hooks

 	#force download  // condition check, it should not be empty, not a directory, and should be readable.
 	$forcedownload = empty($filepath) ? false : !is_file($filepath) ? false : is_readable($filepath);
	if ($forcedownload && file_exists($filepath) && $hasaccess==true) {  // user stat conditional updated  some files are PUBLIC !!!
    header("Content-Description: File Transfer");
    header("Content-Type:".$mime);
    header("Content-Disposition: attachment; filename=".$filename   );
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    header("Content-Length:".$filesize );
		readfile($filepath);}
		}else{
	// bounce them because they tried to link to a protected resource.  It would be nice if we could hook an error msg, but so far I havent./
		wp_redirect( home_url('/sign-in') );
		 exit;
		 }
}//end lpf_librarian
