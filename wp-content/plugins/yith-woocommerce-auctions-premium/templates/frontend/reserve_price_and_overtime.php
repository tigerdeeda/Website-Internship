<?php

$instance = YITH_Auctions()->bids;
$max_bid = $instance->get_max_bid($product->get_id());

if ($product->get_minimum_manual_bid_increment() && $max_bid ) {
    echo '<div id="yith_wcact_manuel_bid_increment" class="yith_wcact_font_size">';
        echo '<p>';
                echo apply_filters('yith_wcact_manual_bid_increment_text',sprintf( __('Enter "%s" or more.', 'yith-auctions-for-woocommerce'),
                    wc_price($product->get_current_bid() + $product->get_minimum_manual_bid_increment())), $product);
        echo '</p>';
    echo '</div>';
}

echo '<div id="yith_wcact_reserve_and_overtime">';

    echo  '<div id="yith_reserve_price" class="yith_wcact_font_size">';
        if ( $product->has_reserve_price() ) {
            if ($max_bid && $max_bid->bid >= $product->get_reserve_price() ){
                _e('ผลิตภัณฑ์เกินราคาสำรอง ', 'yith-auctions-for-woocommerce');
            } else {
                _e('ผลิตภัณฑ์มีราคาจอง ', 'yith-auctions-for-woocommerce');
            }
        } else {
            _e('ผลิตภัณฑ์นี้ไม่มีราคาจอง ', 'yith-auctions-for-woocommerce');
        }
        echo '</div>';

        echo  '<div id="yith-wcact-overtime">';
        if( $product->is_in_overtime() ) {
            ?>
            <span id="yith-wcact-is-overtime"> <?php _e('Currently in overtime','yith-auctions-for-woocommerce')?> </span>
            <?php

        }
    echo '</div>';

echo '</div>';