<?php
/**
 * Email for admin when without any bids
 *
 * @author  Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>


    <p><?php printf( __( "Hi!, we would like to inform you that the auction for the  product:", 'yith-auctions-for-woocommerce' )); ?></p>

    <?php
        $args = array(
            'product'       => $email->object['product'],
            'url'           => $email->object['url_product'],
            'product_name'  => $email->object['product_name'],
        );
        wc_get_template('product-email.php', $args, '', YITH_WCACT_PATH .'templates/emails/product-emails/');
    ?>
    <p><?php printf( __( "Doesn't have any bid", 'yith-auctions-for-woocommerce' )); ?></p>
    <div>
        <p><?php _e( 'If you want to make another action with the product, click this', 'yith-auctions-for-woocommerce' ); ?> <a href="<?php echo $email->object['url_product'];?>"><?php _e( 'link', 'yith-auctions-for-woocommerce' ); ?></a> </p>
    </div>

<?php do_action( 'woocommerce_email_footer', $email );