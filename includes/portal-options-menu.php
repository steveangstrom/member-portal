<?php
/*
add_action('admin_menu', 'sandbox_create_menu_page');
function sandbox_create_menu_page() {
    add_submenu_page(
        'edit.php?post_type=portal-page',          // slug
        'Portal Options',                  // title
        'Options',                      // menu title
        'administrator',            // Which type of users can see this menu
        'portal-options',                  // The unique ID - that is, the slug - for this menu item
        'portal_options_page_display'// The name of the function to call when rendering the menu for this page
    );
} // end sandbox_create_menu_page
*/

add_action( 'admin_init', 'portal_register_settings' );

function portal_register_settings() {

  if (function_exists('wc_get_page_id')){
    $account_loc =  get_permalink( wc_get_page_id( 'myaccount' ));
  }else{
    $account_loc = 'Member Area';
  }


   add_option( 'portal_menu_label', 'Member Area');
   add_option( 'portal_login_location', $account_loc );
   add_option( 'portal_register_location', $account_loc );
   add_option( 'portal_userpage_location', $account_loc );
   add_option( 'portal_menuname', 'primary');
   add_option( 'portal_woo_membership_product', '');

   /*
   add_settings_section(
       'general_settings_section',         // ID used to identify this section and with which to register options
       'Options',                  // Title to be displayed on the administration page
       'portal_general_options_callback', // Callback used to render the description of the section
       'general'                           // Page on which to add this section of options
   );*/

   register_setting( 'portal_options_group', 'portal_menu_label', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_login_location', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_register_location', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_userpage_location', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_menuname', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_woo_membership_product', 'myplugin_callback' );
}


add_action('admin_menu', 'portal_register_options_page');

function portal_register_options_page() {
  //add_options_page('Page Title', 'Portal Options', 'manage_options', 'portal_options', 'portal_options_page');
  add_submenu_page( 'edit.php?post_type=portal-page', 'Page Title', 'Portal Options', 'manage_options', 'portal_options', 'portal_options_page');
}

function member_portal_woo_options_reset(){

    $account_loc =  get_permalink( wc_get_page_id( 'myaccount' ));
  update_option( 'portal_login_location', $account_loc );
  update_option( 'portal_register_location', $account_loc );
  update_option( 'portal_userpage_location', $account_loc );

}



/*********** CAllbacks ************/

function portal_general_options_callback() {
    echo '<p>Select which areas of content you wish to display.</p>';
} // end sandbox_general_options_callback

function portal_options_page(){
?>
  <div>
  <?php screen_icon(); ?>
  <div class="wrap">
  <h2>Portal Options</h2>
  <form method="post" action="options.php">
  <?php settings_fields( 'portal_options_group' ); ?>
  <h3>Options for the Membership portal</h3>

  <table>
  <tr>
  <th scope="row"><label for="portal_login_location">Sign-in Page</label></th>
  <td><input type="text" id="portal_login_location" name="portal_login_location" value="<?php echo get_option('portal_login_location'); ?>" /></td>
  </tr>
  <tr>
  <tr>
  <th scope="row"><label for="portal_register_location">Register Page</label></th>
  <td><input type="text" id="portal_register_location" name="portal_register_location" value="<?php echo get_option('portal_register_location'); ?>" /></td>
  </tr>
  <tr>
  <th scope="row"><label for="portal_userpage_location">User Page</label></th>
  <td><input type="text" id="portal_userpage_location" name="portal_userpage_location" value="<?php echo $page_slug=get_option('portal_userpage_location'); ?>" />
<?php
  $page = get_page_by_path( $page_slug , OBJECT );

   if ( $page ){
     echo 'exists';}else{
    echo '<a class="button">Make</a>'; // no action attached yet.
   }
?>
  </td>
  </tr>
  <tr>
  <th scope="row"><label for="portal_menuname">Menu for Login</label></th>
  <td><input type="text" id="portal_menuname" name="portal_menuname" value="<?php echo get_option('portal_menuname'); ?>" /></td>
  </tr>
  <tr>
  <th scope="row"><label for="portal_menu_label">Menu Label</label></th>
  <td><input type="text" id="portal_menu_label" name="portal_menu_label" value="<?php echo get_option('portal_menu_label'); ?>" /></td>
  </tr>
  <tr>
  <th scope="row"><label for="portal_woo_membership_product">Woo Membership Product</label></th>
  <td><input type="text" id="portal_woo_membership_product" name="portal_woo_membership_product" value="<?php echo get_option('portal_woo_membership_product'); ?>" />(copy product ID from Woo member product)</td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<p><a href="http://localhost/testingzone/wp-admin/admin.php?action=wpse10500&data=foobaridisfromlink">Submit</a><p>
<?php






function create_portal_page(){
  status_header(200);
   die("Server received '{$_REQUEST['data']}' from your browser.");
  /* $post_details = array(
  'post_title'    => $pagetitle,
  'post_content'  => 'This is the portal page',
  'post_status'   => 'publish',
  'post_author'   => 1,
  'post_type' => 'page'
   );
   wp_insert_post( $post_details );*/

}

    if (!function_exists('wc_get_page_id')){
      echo 'WooCommerce is not installed, this plugin works best with WooCommerce asthe login and register interface so you can add paid membership';
    }else{
      global $wp;
      $current =  $wp->request ;
      echo '<a class="button button-primary " href="'.$current.'?stuffhere">Reset</a> all entries to WooCommerce standard pages';
    }

}


add_action( 'admin_menu', 'wpse10500_admin_menu' );
function wpse10500_admin_menu(){
    add_management_page( 'WPSE 10500 Test page', 'WPSE 10500 Test page', 'administrator', 'wpse10500', 'wpse10500_do_page' );
}


add_action( 'admin_action_wpse10500', 'wpse10500_admin_action' );
function wpse10500_admin_action(){
    // Do your stuff here
    echo('yep');
    status_header(200);
       die("Server received '{$_REQUEST['data']}' from your browser.");
    wp_redirect( $_SERVER['HTTP_REFERER'] );
    exit();
}


function wpse10500_do_page(){
?>
<a href="http://localhost/testingzone/wp-admin/admin.php?action=wpse10500&data=foobaridisfromlink">Submit</a>
<form method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
    <input type="hidden" name="action" value="wpse10500" />
    <input type="hidden" name="data" value="foobarid blah blah balh ">
    <input type="submit" value="Do it!" />
</form>
<?php
}
