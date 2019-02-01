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
   add_option( 'portal_option', 'This is my default option value.');
   add_option( 'portal_login_location', 'page location');
   add_option( 'portal_userpage_location', 'user homepage location');
   /*
   add_settings_section(
       'general_settings_section',         // ID used to identify this section and with which to register options
       'Options',                  // Title to be displayed on the administration page
       'portal_general_options_callback', // Callback used to render the description of the section
       'general'                           // Page on which to add this section of options
   );*/

   register_setting( 'portal_options_group', 'portal_option', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_login_location', 'myplugin_callback' );
   register_setting( 'portal_options_group', 'portal_userpage_location', 'myplugin_callback' );
}


add_action('admin_menu', 'portal_register_options_page');
function portal_register_options_page() {
  add_options_page('Page Title', 'Portal Options', 'manage_options', 'portal_options', 'portal_options_page');


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
  <h3>This is my option</h3>
  <p>Some text here.</p>
  <table>
  <tr>
  <th scope="row"><label for="portal_option">Label</label></th>
  <td><input type="text" id="portal_option" name="portal_option" value="<?php echo get_option('portal_option'); ?>" /></td>
  </tr>
  <tr>
  <th scope="row"><label for="portal_login_location">Login Page location</label></th>
  <td><input type="text" id="portal_login_location" name="portal_login_location" value="<?php echo get_option('portal_login_location'); ?>" /></td>
  </tr>
  <tr>
  <th scope="row"><label for="portal_userpage_location">User Page location</label></th>
  <td><input type="text" id="portal_userpage_location" name="portal_userpage_location" value="<?php echo get_option('portal_userpage_location'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
}
