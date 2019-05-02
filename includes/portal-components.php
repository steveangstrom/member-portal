<?php
function portal_show_user_status(){

}


function get_personal_portal_items(){
	global $username;
	$out='';
	$out.=('<li><a href="'.wp_logout_url( home_url() ).'">Logout ('.$username.')</a></li>');
	$out.=('<li><a href="'.wp_lostpassword_url().'" title="Lost Password">Change Password</a></li>');
	return $out;
}

add_filter( 'the_content', 'portal_content_append' );

function portal_content_append( $content ) {
	// make a filter for this so users can add arbitrary content to the member portal page.
	if (is_portal_content()){
		$content=$content.'<br>Is portal content<br>';
		 // $prod_arr = array( '3202', '3203' );
		$member_product =  get_option('portal_woo_membership_product');
		$membership_status=has_bought_membership(array(	$member_product ));

		if ($membership_status['bought']){
			 $ordertime = strtotime($membership_status['order_date']);
			 $membership_duration = $membership_status['variation_atts']['attribute_duration'];
			 $elapsetime = strtotime($membership_status['order_date'].'+ '.$membership_duration.' months');
			 $elapsetimeformat = date('d-m-Y',$elapsetime);
			 $newformat = date('d-m-Y',$ordertime);
			$content= $content.'This user has bought membership on the date of '.$membership_status['order_date'].
			'  and  a variation of '.$membership_status['variation_id'] .
			 ' The original date: '.$newformat.' plus the duration of '.$membership_duration.' months means this membership elapses on '.$elapsetimeformat;

		}
	}

	return $content;
}

function is_portal_content(){
	global $post;
	$isportal = $post->post_type =='portal-page' ? true : false;
	return $isportal;
}
