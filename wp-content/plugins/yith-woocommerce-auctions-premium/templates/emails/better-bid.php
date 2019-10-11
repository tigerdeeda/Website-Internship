<?php
/**
 * Email for user when another user just overbid your maximun bid
 *
 * @author  Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php _e( 'Another user just overbidded your maximum bid', 'yith-auctions-for-woocommerce' ); ?></p>
	<p><?php printf( __( "Hi!  %s, Another user has just overbidded your maximum bid for the product:", 'yith-auctions-for-woocommerce' ),
			$email->object['user_name']);

		?></p>

	<?php
		$args = array(
			'product' 		=> $email->object['product'],
			'url'           => $email->object['url_product'],
			'product_name'  => $email->object['product_name'],
		);
		wc_get_template('product-email.php', $args, '', YITH_WCACT_PATH .'templates/emails/product-emails/');
	?>

	<div>
		<p><?php _e( 'If you want to bid a new amount, click this', 'yith-auctions-for-woocommerce' ); ?> <a href="<?php echo $email->object['url_product'];?>"><?php _e( 'link', 'yith-auctions-for-woocommerce' ); ?></a> </p>
	</div>

<?php do_action( 'woocommerce_email_footer', $email );