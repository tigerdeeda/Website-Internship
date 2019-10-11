<?php
$bid_increment   = ( $bid = yit_get_prop( $product, '_yith_auction_bid_increment', true ) ) ?  $bid  : '1';

//code for show overtime and bidup//
$showbidup = yit_get_prop($product,'_yith_wcact_upbid_checkbox', true);
$bidup = "";
if ( 'yes' == $showbidup ) {
    $bidup = ( $bid = yit_get_prop( $product, '_yith_auction_bid_increment', true )) ? __('Bid up: ','yith-auctions-for-woocommerce') . wc_price($bid) :  __('Bid up: No bid up','yith-auctions-for-woocommerce') ;
}

$showoverbid = yit_get_prop($product,'_yith_wcact_overtime_checkbox', true);
$over = "";
if ( 'yes' == $showoverbid ) {
    $over = ( $overtime = $product->get_overtime()) ?  sprintf(_x( 'Overtime: %s min','Overtime: 3 min', 'yith-auctions-for-woocommerce' ), $overtime) : __('No overtime','yith-auctions-for-woocommerce')  ;
}
?>
<div class="yith-wcact-max-bidder" id="yith-wcact-max-bidder">
        <div class="yith-wcact-overbidmode yith-wcact-bidupmode">
            <span id="yith-wcact-showbidup"><?php echo $bidup ?></span> <span title="<?php echo __('Total used from pool of money for automatic bid up.','yith-auctions-for-woocommerce') ?>" <?php echo ('yes' == $showbidup) ? 'class="yith-auction-help-tip"': '' ?>></span> </br>
            <span id="yith-wcact-showovertime"><?php echo $over ?> </span> <span title="<?php echo __('จำนวนนาทีที่เพิ่มในการประมูลหากมีการเสนอราคาภายในช่วงการทำงานล่วงเวลา','yith-auctions-for-woocommerce')?>" <?php echo ( 'yes' == $showoverbid) ? 'class="yith-auction-help-tip"': '' ?>></span>
        </div>
    <?php
    ////////////////////////////////////
    
    $instance = YITH_Auctions()->bids;
    $max_bid = $instance->get_max_bid($product->get_id());
    $userid = get_current_user_id();
    
    if ( $max_bid && $userid == $max_bid->user_id) {
        ?>
        <div id="winner_maximun_bid">
            <p id="max_winner"><?php _e(' ขณะนี้คุณเป็นผู้เสนอราคาสูงสุดสำหรับการประมูลนี้','yith-auctions-for-woocommerce')?> <span title="<?php echo __('รีเฟรชหน้าเว็บเป็นประจำเพื่อดูว่าคุณยังคงเป็นผู้เสนอราคาสูงสุดหรือไม่','yith-auctions-for-woocommerce') ?>" class="yith-auction-help-tip"></span></p>
            <?php
            $show_tooltip = ( $bid = yit_get_prop( $product, '_yith_auction_bid_increment', true )) ? '<span title="'. __('If your bid is higher or equivalent to the reserve price, your bid will match the reserve price with the remaining saved and used automatically to outbid a competitors bid.','yith-auctions-for-woocommerce').'" class="yith-auction-help-tip"></span>': '';
            ?>
            <p id="current_max_bid"><?php echo sprintf( apply_filters('yith_wcact_current_max_bid',_x( 'ราคาเสนอสูงสุดของคุณ: %s','ราคาเสนอสูงสุดของฉัน: $ 50.00', 'yith-auctions-for-woocommerce' ),$show_tooltip), wc_price( $max_bid->bid ) ) ?> <?php echo $show_tooltip ?></p>
        </div>
        <?php
    }
    ?>
</div>
    