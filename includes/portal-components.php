<?php



function has_bought_membership($prod_arr) {
    $bought = false;

    // Set HERE ine the array your specific target product IDs
  //  $prod_arr = array( '3202', '3203' );

    // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => 'shop_order', // WC orders post type
        'post_status' => 'wc-completed' // Only orders with status "completed"
    ) );
    foreach ( $customer_orders as $customer_order ) {
        // Updated compatibility with WooCommerce 3+
        $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
        $order = wc_get_order( $customer_order );
        $order_date = $order->order_date;

        // Iterating through each current customer products bought in the order
        foreach ($order->get_items() as $item) {


            // WC 3+ compatibility
            if ( version_compare( WC_VERSION, '3.0', '<' ) )
                $product_id = $item['product_id'];
            else
                $product_id = $item->get_product_id();
            // Your condition related to your 2 specific products Ids
            if ( in_array( $product_id, $prod_arr )) {
              $bought = true;
              // now get the variation to get the expiry date
              $product = $item->get_product();
              $variation_id = $item->get_variation_id();
              $variation_arr = wc_get_product($variation_id);
              $variation_atts = $variation_arr->get_variation_attributes();
            }
           }
    }
    // return "true" if one the specifics products have been bought before by customer
    return array('bought'=>$bought, 'order_date'=>$order_date, 'variation_id'=>$variation_id, 'variation_atts'=>$variation_atts);
}




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

/* ----------  component to get  membersip details  ------------- */

function get_portal_membership_status( $content='' ) {
		$member_product =  get_option('portal_woo_membership_product');
		$membership_status=has_bought_membership(array(	$member_product ));

		if ($membership_status['bought']){
			 $ordertime = strtotime($membership_status['order_date']);
			 $membership_duration = $membership_status['variation_atts']['attribute_duration'];
			 $elapsetime = strtotime($membership_status['order_date'].'+ '.$membership_duration.' months');
			 $elapsetimeformat = date('d-m-Y',$elapsetime);
			 $newformat = date('d-m-Y',$ordertime);
			$content= 'This user has bought membership on the date of '.$membership_status['order_date'].
			'  and  a variation of '.$membership_status['variation_id'] .
			 ' The original date: '.$newformat.' plus the duration of '.$membership_duration.' months means this membership elapses on '.$elapsetimeformat;
	}
	return $content;
}
/* ----------  component to display  membersip details  ------------- */
function show_portal_membership_status(  ) {
	$content='<div class="portal_member_details portal_box">';
	$content= get_portal_membership_status();
	$content.= '</div>';
	echo $content;
}


function is_portal_content(){
	global $post;
	$isportal = $post->post_type =='portal-page' ? true : false;
	return $isportal;
}
