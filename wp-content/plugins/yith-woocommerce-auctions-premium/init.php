<?php
/*
Plugin Name: YITH Auctions for WooCommerce Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-auctions-for-woocommerce/
Description: YITH Auctions for WooCommerce Premium plugin allows buyers to grab products for their best price and allows vendors to generate a profit sometimes bigger than anticipated.
Author: YITHEMES
Text Domain: yith-auctions-for-woocommerce
Version: 1.1.2
Author URI: http://yithemes.com/
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! function_exists( 'yith_wcact_install_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if WooCommerce is deactivated
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wcact_install_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php _ex( 'YITH WooCommerce Auctions is enabled but not effective. It requires WooCommerce in order to work.', 'Alert Message: WooCommerce requires', 'yith-auctions-for-woocommerce' ); ?></p>
        </div>
        <?php
    }
}


/**
 * Check if WooCommerce is activated
 *
 * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
 * @since 1.0
 * @return void
 * @use admin_notices hooks
 */
if( ! function_exists( 'yith_wcact_install' ) ) {

    function yith_wcact_install()
    {

        if (!function_exists('WC')) {
            add_action('admin_notices', 'yith_wcact_install_woocommerce_admin_notice');
        } else {
            do_action('yith_wcact_init');
            YITH_WCACT_DB::install();
        }
    }

    add_action( 'plugins_loaded', 'yith_wcact_install', 11 );
}


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';                                      
}
yit_deactive_free_version( 'YITH_WCACT_FREE_INIT', plugin_basename( __FILE__ ) );


/* === DEFINE === */
! defined( 'YITH_WCACT_VERSION' )            && define( 'YITH_WCACT_VERSION', '1.1.2' );
! defined( 'YITH_WCACT_INIT' )               && define( 'YITH_WCACT_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCACT_SLUG' )               && define( 'YITH_WCACT_SLUG', 'yith-woocommerce-auctions' );
! defined( 'YITH_WCACT_SECRETKEY' )          && define( 'YITH_WCACT_SECRETKEY', 'zd9egFgFdF1D8Azh2ifK' );
! defined( 'YITH_WCACT_FILE' )               && define( 'YITH_WCACT_FILE', __FILE__ );
! defined( 'YITH_WCACT_PATH' )               && define( 'YITH_WCACT_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCACT_URL' )                && define( 'YITH_WCACT_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCACT_ASSETS_URL' )         && define( 'YITH_WCACT_ASSETS_URL', YITH_WCACT_URL . 'assets/' );
! defined( 'YITH_WCACT_TEMPLATE_PATH' )      && define( 'YITH_WCACT_TEMPLATE_PATH', YITH_WCACT_PATH . 'templates/' );
! defined( 'YITH_WCACT_WC_TEMPLATE_PATH' )   && define( 'YITH_WCACT_WC_TEMPLATE_PATH', YITH_WCACT_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCACT_OPTIONS_PATH' )       && define( 'YITH_WCACT_OPTIONS_PATH', YITH_WCACT_PATH . 'panel' );
! defined( 'YITH_WCACT_PREMIUM' )            && define( 'YITH_WCACT_PREMIUM', true );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCACT_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCACT_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCACT_PATH  );                  


function yith_wcact_init_premium() {
    load_plugin_textdomain( 'yith-auctions-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


    if ( ! function_exists( 'YITH_Auctions' ) ) {
        /**
         * Unique access to instance of YITH_Auction class
         *
         * @return YITH_Auctions
         * @since 1.0.0
         */
        function YITH_Auctions() {

            require_once( YITH_WCACT_PATH . 'includes/class.yith-wcact-auction.php' );
            if ( defined( 'YITH_WCACT_PREMIUM' ) && file_exists( YITH_WCACT_PATH . 'includes/class.yith-wcact-auction-premium.php' ) ) {

                require_once( YITH_WCACT_PATH . 'includes/class.yith-wcact-auction-premium.php' );
                return YITH_Auctions_Premium::instance();
            }
            return YITH_Auctions::instance();
        }
    }

   // Let's start the game!
   YITH_Auctions();
}

add_action( 'yith_wcact_init', 'yith_wcact_init_premium' );