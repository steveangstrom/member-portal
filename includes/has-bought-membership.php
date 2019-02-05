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
              $variation = $item->get_variation_id();

              echo'<pre>';
              print_r($variation);
              echo'</pre>';
            }
           }
    }
    // return "true" if one the specifics products have been bought before by customer
    return array('bought'=>$bought, 'order_date'=>$order_date, 'variation'=>$variation);
}
