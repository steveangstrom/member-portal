<?php

 /***** Content display in the LEARNING PORTAL PAGE *******
 We append this to the page which has the slug learning-portal
 *****/

 /*
 22-06-16 SIM fixed uninitialized vars which were throwing notices ($date,$meta,$recent)
 27-06-16 STEVE - this page is being phased out in favour of the template. in the /plugin/template folder. because of reasons.
 things to DO

 include big boxes with: metasearch for

 	Meta "Member Reports Only"
  	Meta "Future Conferences"
  	Meta "Member Only Publications"

 */



 /**
   * Register new endpoints to use inside My Account page.
   */

  add_action( 'init', 'my_account_new_endpoints' );
  function my_account_new_endpoints() {
  	add_rewrite_endpoint( 'portal', EP_ROOT | EP_PAGES );
  }

 function member_display_portal_content( $content ) {
// if is not logged in show them a different thing ... not done yet
  global $username;
	$slug = get_post_field( 'post_name', get_post());
  $userpage=get_option('portal_userpage_location');
	if( $slug ==$userpage){
		 if ( !is_user_logged_in() ) {
			 $content = '<h2>Please sign in to the Portal</h2>';
		 }else{

		$user_ID = get_current_user_id();
		$usermeta = get_user_meta( $user_ID );
		$username = $usermeta['nickname'][0];// is actually their email now

    /*echo('<pre>');
    print_r($usermeta['membership_status'][0]);
    echo('</pre>');*/

		$userdata = get_userdata( $user_ID );
		$explodedEmail = explode('@', $userdata->data->user_email);
		$user_domain = array_pop($explodedEmail);


		/*$org_auth = get_organisation_auth($userdata->data->user_email);// func is in frontend-reg-and-login plug

		if (!$org_auth){
			pher_errors()->add('not_authorised', __('Your organisation does not have the required access permission. Please contact your network administrator or email hello@staffcollege.uk if you require access.'));
		}*/

		// show any error messages

			$head='';
			$lowermeta='';
      
			if(!empty($usermeta['first_name'][0])){
				$content = '<h3>Hi '.$usermeta['first_name'][0].',<br>Welcome to the  portal</h3>'.$content;
			}else{
				$content = '<h3>Hi '.$usermeta['nickname'][0].',<br>Welcome to the portal</h3>'.$content;
			}

			if(!empty($usermeta['organisation'][0])){
					 $content =  '<h4>From '.$usermeta['organisation'][0].'</h4>'.$content;
				 }

  // $content = get_portal_membership_status().$content;   # removed a

		/*
			if(!empty($usermeta['_last_login'][0])){
				$last_login =$usermeta['_last_login'][0];
				$date = DateTime::createFromFormat( 'U', $last_login );
				$nice_date=date_format($date,"F j, Y");
				$content = $content. 'Your previous sign in was on the '. $nice_date;

				}else{
					$content = $content. '<div class="visit_date">As this is <em>your first visit</em> to the learning portal, why not check out these recently updated member-only resources</div>';
					$date='';
			}
			*/
			$content = $content.'<div class="portal_box"><h3>Subscriber resources</h3>'.get_recent_portal_items($date,'publications',true).'</div>';

			$dateatts = array(	'show' => '3','order' => 'DESC','excerptlength' => '100','displaymode' => 'small','showexpired' => 'nope');
			//$content = $content.'<div class="portal_box"><h3>Future Conferences</h3>'.pher_display_some_events($dateatts).'</div>';

			$content = $content.'<div class="portal_box"><h3>Multimedia</h3>'.get_recent_portal_items('','multimedia').'</div>';
			return  $content.$lowermeta ;
		}
	}

	return $content;
}

add_filter( 'the_content', 'member_display_portal_content' );






function get_recent_portal_items($date='',$category='',$protection='',$shownum=5){

	$meta_query = array();// show all - default

	if($protection!=''){// show only protected content CONDITIONAL
		/*$meta_query = array(
			'key' => 'resource_protection',
			'value' => 'protected',
			'compare' => '=='
		)*/
		;$meta_query = array(
			'key' => 'event_file_list',
			'compare' => 'EXISTS',
		);


	}

	$date_query  = array(
			'column' => 'post_date_gmt',  // not sure if post_modified_gmt is the right key. because that includes edits.  // ALTERNATIVE  post_date_gmt
			'after'  => '1 year ago',
		);

	$since_args = array( 'post_type' => 'post','posts_per_page' => $shownum, 'category_name' => $category, 'meta_query' => array($meta_query)/*,'date_query'  => array($date_query )*/);

	$since_loop = new WP_Query( $since_args );

	 $out=  '';
	// $out .='<p>since your previous visit ('.$nice_date=date_format($date,"F j, Y").')</p>';

	if($since_loop->have_posts()) :while ( $since_loop->have_posts() ) : $since_loop->the_post();
		$title=get_the_title();
		$thumb = get_the_post_thumbnail (null,array(100,100));
		$thedate = the_date('F j, Y', '<span class="publishdate">', '</span>',false);
		$excerpt = get_the_excerpt();

		//$out.=('<div class="recent_publication"><a href="'.get_the_permalink().'" title = "Read more about '.$title.'"  ><h5>'.$thedate.' '. $title.'</h5>'.$thumb.'<p class="excerpt">'. $excerpt.'</p></a></div>');

		$out.=('<a href="'.get_the_permalink().'" title = "Read more about '.$title.'" class="event_listing_item" >');
		$out.='<div class="event_post latest_post">';
		//$out.='<div class="event_thumb latest_post_image"></div>';
		$out.='<div class="latest_post_image">'.$thumb.'</div>';
		$out.='<div class="latest_post_text">';
		//$out.='<div class="event-headline"><span class="eventdate">'.$thedate. '</span> '. $title.'</div>';
		$out.='<div class="event-headline">'. $title.'</div>';
		$out.='<div class="event-excerpt">'.$excerpt.'</div>';
		$out.='</div>';
		$out.='</div></a>';
	endwhile;
	endif;

	/***********************/

/*
	$out.='<hr>';
	 $out.=  '<h3>Member Reports Only</h3>';
	$args = array( 'post_type' => 'post','posts_per_page' => $shownum, 'category_name' => 'publications');
	$loop = new WP_Query( $args );

	if($loop->have_posts()) :while ( $loop->have_posts() ) : $loop->the_post();
		$title=get_the_title();
		$thedate = the_date('F j, Y', '<span class="publishdate">', '</span>',false);
		$excerpt = get_the_excerpt();
		$out.=('<div class="recent_publication"><a href="'.get_the_permalink().'" title = "Read more about '.$title.'"  ><h5>'.$thedate.' '. $title.'</h5><p class="excerpt">'. $excerpt.'</p></a></div>');


	endwhile;
	endif;
		*/
	/**/

	return $out;
}


?>
