<?php
require_once('wp-load.php');

$orders = wc_get_orders( array('numberposts' => -1) );

$fp = fopen('cog_export.csv', 'w');
fputcsv($fp, array('Order ID', 'Status', 'Total' ,'COG from DB', 'COG calculated'));


// Loop through each WC_Order object
foreach( $orders as $order ){
        $cog_calculated = 0;
        foreach ($order->get_items() as $item_key => $item ):
                ## Using WC_Order_Item methods ##
                // Item ID is directly accessible from the $item_key in the foreach loop or
                $product_id1   = $item->get_product_id(); // the Product id
                $variation_id1 = $item->get_variation_id(); // the Variation id
                if(strlen(get_post_meta($variation_id1, 'yith_cog_cost', true))) {
                        $cog_calculated = $cog_calculated + (get_post_meta($variation_id1, 'yith_cog_cost', true) * $item->get_quantity());
                } else {
                        $cog_calculated = $cog_calculated + (get_post_meta($product_id1, 'yith_cog_cost', true) * $item->get_quantity());
                }
        endforeach;
        $color = 'black';
        if($cog_calculated > $order->get_subtotal()) {
                $color = 'red';
        }
    echo '<span style="color: '.$color.';">'.$order->get_id() . ' - '. $order->get_status() . ' - Total: '.number_format($order->get_subtotal(), 2).' - COG from DB '.number_format($order->get_meta('_yith_cog_order_total_cost'), 2).' -=- COG Calculated '. number_format($cog_calculated, 2)."</span><br />";
        fputcsv($fp, array($order->get_id(), $order->get_status(), number_format($order->get_subtotal(), 2), number_format($order->get_meta('_yith_cog_order_total_cost'), 2), number_format($cog_calculated, 2)));
}

fclose($fp);

echo '<br /><br />';
echo '<a href="cog_export.csv">Download CSV</a>';
