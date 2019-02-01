
<?php
/* this file and functionality is currently removed pending being replaced
by the ability for users to simply connect ACF fileds and blocks to memberpage/portal
 memberportal_custom_post_types_registration
 */
add_action( 'cmb2_admin_init', 'cmb2_memberportal_metaboxes' );

function cmb2_memberportal_metaboxes() {

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
