<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;
/*
add_action( 'carbon_fields_register_fields', 'crb_attach_theme_options' );
function crb_attach_theme_options() {
    Container::make( 'theme_options', __( 'Theme Options' ) )
        ->add_fields( array(
            Field::make( 'text', 'crb_text', 'Text Field' ),
        ) );
}
*/

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
  $dir = plugin_dir_path( __FILE__ );
    require_once( $dir . '/vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
    portal_make_member_content_block();
    portal_show_user_details();
}

function portal_make_member_content_block(){
Block::make( __( 'Member Content Block' ) )
    ->add_fields( array(
        Field::make( 'text', 'heading', __( 'Block Heading' ) ),
        Field::make( 'image', 'image', __( 'Block Image' ) ),
        Field::make( 'rich_text', 'content', __( 'Block Content' ) ),
    ) )->set_category( 'Member Portal' )
    ->set_icon( 'groups' )
    ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>

        <div class="block">
            <div class="block__heading">
                <h1><?php echo esc_html( $fields['heading'] ); ?></h1>
            </div><!-- /.block__heading -->

            <div class="block__image">
                <?php echo wp_get_attachment_image( $fields['image'], 'full' ); ?>
            </div><!-- /.block__image -->

            <div class="block__content">
                <?php echo apply_filters( 'the_content', $fields['content'] ); ?>
            </div><!-- /.block__content -->
        </div><!-- /.block -->

        <?php
    } );
  }

  function portal_show_user_details(){
  Block::make( __( 'Member Details' ) )
      ->add_fields( array(
          Field::make( 'text', 'heading', __( 'Block Heading' ) ),
      ) )->set_category( 'Member Portal' )
      ->set_icon( 'groups' )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
          ?>

          <div class="block">
              <div class="block__heading">
                  <h3><?php echo esc_html( $fields['heading'] ); ?></h3>
              </div><!-- /.block__heading -->
              <div class="block__content">
                  <?php echo show_portal_membership_status(); ?>
              </div><!-- /.block__content -->
          </div><!-- /.block -->

          <?php
      } );
    }
