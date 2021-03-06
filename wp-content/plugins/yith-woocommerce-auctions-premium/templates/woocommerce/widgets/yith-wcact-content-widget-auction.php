<?php
/**
 * The template for displaying product widget entries
**/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$to_auction   = ( $datetime = yit_get_prop( $product, '_yith_auction_to', true ) ) ?  $datetime  : NULL;
$auction_finish = $to_auction;
$date = strtotime('now');
$total = $auction_finish - $date;
?>



<li>
	<a class="yith-wcact-widget-image" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
		<?php echo $product->get_image(array(40,40)); ?>
		<span class="product-title"><?php echo $product->get_title(); ?></span>
	</a>
	<?php if ( ! empty( $show_rating ) ) : ?>
		<?php echo $product->get_rating_html(); ?>
	<?php endif; ?>
	<p><?php echo __('Current bid:','yith-auctions-for-woocommerce');?> <?php echo wc_price($product->get_price()); ?></p>
	<?php
		if ( $product->is_start()) {
	?>
			<div class="time_widget_product" id="time_widget" data-remaining-time-<?php echo $product->get_id()  ?>=" <?php echo $total ?>" data-product-id="<?php echo $product->get_id() ?>">
				<label
					for="yith_time_left"><?php _e('Time left to end auction:', 'yith-auctions-for-woocommerce') ?></label>
				<div class="timer" id="timer_widget">
					<span class="days_widget" id="days_widget_<?php echo $product->get_id() ?>"></span>days
					<span class="hours_widget" id="hours_widget_<?php echo $product->get_id() ?>"></span>hours
					<span class="minutes_widget" id="minutes_widget_<?php echo $product->get_id() ?>"></span>minutes
					<span class="seconds_widget" id="seconds_widget_<?php echo $product->get_id() ?>"></span>seconds
				</div>
			</div>
	<?php
		}
	?>

</li>
