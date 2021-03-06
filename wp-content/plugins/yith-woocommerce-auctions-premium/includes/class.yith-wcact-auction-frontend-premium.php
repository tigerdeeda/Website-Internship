<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCACT_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Auctions_Frontend
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Auction_Frontend_Premium' ) ) {
    /**
     * Class YITH_Auctions_Frontend
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Auction_Frontend_Premium extends YITH_Auction_Frontend
    {

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            add_filter('woocommerce_catalog_orderby', array($this, 'sort_auctions'));
            add_filter('woocommerce_get_catalog_ordering_args', array($this, 'ordering_auction'));

            //cart
            add_filter('woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session'), 10);
            add_filter('woocommerce_add_cart_item', array($this, 'add_cart_item'), 10);
            add_filter('woocommerce_add_cart_item_data', array($this, 'add_cart_item_yith_auction_data'), 10, 2);

            add_action('pre_get_posts', array($this, 'modify_query_loop'));

            add_action('woocommerce_checkout_order_processed', array($this, 'finish_auction'), 10, 2);

            add_filter('yith_wcact_before_form_add_to_cart',array($this,'check_closed_for_buy_now'),10,2);
            add_action('yith_wcact_in_to_form_add_to_cart',array($this,'check_if_max_bid_and_reserve_price'));
            add_action('yith_wcact_auction_before_set_bid',array($this,'add_auction_timeleft'));
            add_action('yith_wcact_after_form_bid',array($this,'if_reserve_price'));
            add_action('yith_wcact_after_add_button_bid',array($this,'add_button_buy_now'));
            add_action('yith_wcact_after_add_to_cart_form',array($this,'add_watch_list_button'));
            add_action('wp_loaded', array($this, 'add_to_watchlist'), 90);

            parent::__construct();
        }

        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Frontend
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.9
         * @return void
         */
        public function enqueue_scripts()
        {
            global $post;
            wp_register_style('yith-wcact-frontend-css', YITH_WCACT_ASSETS_URL . 'css/frontend.css');
            wp_register_style('yith-wcact-widget-css', YITH_WCACT_ASSETS_URL . 'css/yith-wcact-widget.css');

            /* === Script === */
            wp_register_script('yith-wcact-frontend-js-premium', YITH_WCACT_ASSETS_URL . 'js/frontend-premium.js', array('jquery', 'jquery-ui-datepicker'), '1.0.0', 'true');
            wp_register_script('yith-wcact-widget-js', YITH_WCACT_ASSETS_URL . 'js/yith-wcact-widget.js', array('jquery'), '1.0.0', 'true');

            //Localize scripts for ajax call
            wp_localize_script('yith-wcact-frontend-js-premium', 'object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'live_auction_product_page' => get_option('yith_wcact_settings_live_auction_product_page') * 1000
            ));

            if (apply_filters('yith_wcact_load_script_everywhere', false) || is_shop() || is_archive()) {
                /* === CSS === */
                wp_enqueue_style('yith-wcact-frontend-css');

                /* === Script === */

                wp_enqueue_script('yith_wcact_frontend_shop_premium', YITH_WCACT_ASSETS_URL . 'js/fontend_shop-premium.js', array('jquery', 'jquery-ui-sortable'), YITH_WCACT_VERSION, true);
                wp_localize_script('yith_wcact_frontend_shop_premium', 'object', array(
                    'ajaxurl' => admin_url('admin-ajax.php')
                ));
            }

            if ( apply_filters('yith_wcact_load_script_widget_everywhere', false) || is_active_widget(false, false, 'yith_woocommerce_auctions', true) ) {
                /* === Script === */
                wp_enqueue_script('yith-wcact-widget-js');
                /* === CSS === */
                wp_enqueue_style('yith-wcact-widget-css');

            }

            if (is_product()) {
                $product = wc_get_product($post->ID);
                if ( 'auction' == $product->get_type() ) {
                    /* === CSS === */
                    wp_enqueue_style('yith-wcact-frontend-css');
                    /* === Script === */
                    wp_enqueue_script('yith-wcact-frontend-js-premium');
                }
            }

            $endpoint = YITH_Auctions()->endpoint;
            if( 'my-auction' == $endpoint->get_current_endpoint() ) {
                wp_enqueue_script('yith_wcact_frontend_endpoint', YITH_WCACT_ASSETS_URL . '/js/frontend-endpoint-premium.js', array('jquery', 'jquery-ui-sortable'), YITH_WCACT_VERSION, true);
                wp_localize_script('yith_wcact_frontend_endpoint', 'object', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'time_check' => get_option('yith_wcact_settings_live_auction_my_auctions') * 1000,
                ));
                wp_enqueue_style('yith-wcact-frontend-css');

            }

            do_action('yith_wcact_enqueue_fontend_scripts');

        }



        /**
         * Sort Auction
         *
         * ​Add to WooCommerce sorting select (in shop page) Sort auctions
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */
        public function sort_auctions($tabs)
        {
            $sort = array(
                'auction_asc' => __('Sort auctions by end date (asc)', 'yith-auctions-for-woocommerce'),
                'auction_desc' => __('Sort auctions by end date (desc)', 'yith-auctions-for-woocommerce'),
            );
            $tabs = array_merge($tabs, $sort);

            return $tabs;
        }

        /**
         * Sort Auction
         *
         * ​Add to WooCommerce sorting select (in shop page) Sort auctions
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */
        public function ordering_auction($args)
        {
            $orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));

            if ($orderby_value == 'auction_asc') {
                $args['orderby'] = 'meta_value';
                $args['order'] = 'ASC';
                $args['meta_key'] = '_yith_auction_to';
            }

            if ($orderby_value == 'auction_desc') {
                $args['orderby'] = 'meta_value';
                $args['order'] = 'DESC';
                $args['meta_key'] = '_yith_auction_to';
            }

            return $args;
        }

        /**
         * Auction end
         *
         * ​Show the Auction end or show the auction start if the auction start after today's date (in shop page)
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */
        public function auction_end_start()
        {
            global $product;
            $product = apply_filters('yith_wcact_get_auction_product',$product);

            if ('auction' == $product->get_type()) {

                $auction_start = yit_get_prop($product, '_yith_auction_for', true);
                $auction_end = yit_get_prop($product, '_yith_auction_to', true);
                $date = strtotime('now');

                if ($date < $auction_start) {
                    echo '<div id="auction_end_start">';
                    echo sprintf(_x('เริ่มต้นประมูล:', 'Auction end: 10 Jan 2016 10:00', 'yith-auctions-for-woocommerce'));
                    echo '<p class="date_auction" data-yith-product="' . $product->get_id() . '">' . $auction_start . '</p>';
                    echo '</div>';
                } else {
                    if (!empty($auction_end) && !$product->is_closed() && !$product->is_closed_for_buy_now()) {
                        echo '<div id="auction_end_start">';
                        echo sprintf(_x('สิ้นสุดการประมูล:', 'Auction end: 10 Jan 2016 10:00', 'yith-auctions-for-woocommerce'));
                        echo '<p class="date_auction">' .  date(wc_date_format() . ' ' . wc_time_format(), $auction_end) . '</p>';
                        //echo '<p class="date_auction" data-yith-product="' . $product->get_id() . '">' . $auction_end . '</p>';
                        echo '</div>';
                    }
                }

            }
        }

        /**
         * Change text button
         *
         * Change text Auction button (in shop page)
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function change_button_auction_shop($text, $product)
        {
            $product = apply_filters('yith_wcact_get_auction_product',$product);

            if ('auction' == $product->get_type() && !$product->is_closed() && !$product->is_closed_for_buy_now()) {
                return __('ประมูลเดี๋ยวนี้', 'yith-auctions-for-woocommerce');
            }

            return $text;
        }


        /**
         * Badge Shop
         *
         * Add a badge if product type is: auction (in shop page)
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */

        public function auction_badge_shop()
        {
            global $product;
            $img = get_option('yith_wcact_appearance_button');
            if ('auction' == $product->get_type() && $img) {
                echo '<span class="yith-wcact-aution-badge"><img src="' . $img . '"></span>';
            }
        }

        /**
         * Badge single product
         *
         * Add a badge if product type is: auction (in simple product)
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */
        public function add_badge_single_product($output)
        {
            global $product;
            $img = get_option('yith_wcact_appearance_button');
            if ('auction' == $product->get_type() && $img) {
                $output .= '<span class="yith-wcact-aution-badge"><img src="' . $img . '"></span>';
            }

            return $output;
        }


        /**
         *  Add cart item data
         *
         *  Create a new array yith_auction_data in $cart_item_data
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return $cart_tem_data
         */
        public function add_cart_item_yith_auction_data($cart_item_data, $product_id)
        {
            $product_id = apply_filters('yith_wcact_auction_product_id',$product_id);
            $terms = get_the_terms($product_id, 'product_type');
            $product_type = !empty($terms) && isset(current($terms)->name) ? sanitize_title(current($terms)->name) : 'simple';
            if ('auction' === $product_type && !isset($cart_item_data['yith_auction_data'])) {
                $cart_item_data['yith_auction_data'] = array(
                    'buy-now' => true
                );
            }

            return $cart_item_data;
        }

        /**
         *  Change price in cart item
         *
         *  If the product_type = 'auction' and click in buy_now change price to buy_now_price
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return $cart_tem_data
         */
        public function add_cart_item($cart_item_data)
        {

            if (isset($cart_item_data['yith_auction_data']) && isset($cart_item_data['yith_auction_data']['buy-now'])) {
                $product = $cart_item_data['data'];
                if (!$product->get_buy_now_price()) {
                    wc_add_notice(__('You cannot purchase this product because it is an Auction!', 'yith-auctions-for-woocommerce'), 'error');
                    return false;
                }
                $buy_now_price = $product->get_buy_now_price();
                $product->set_price($buy_now_price);
                $cart_item_data['data'] = $product;
            }


            return $cart_item_data;
        }

        /**
         *  Load cart from session
         *
         *  If the product_type = 'auction' change the price in the session cart
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return $cart_tem_data
         */

        public function get_cart_item_from_session($session_data)
        {
            if (isset($session_data['yith_auction_data']) && isset($session_data['yith_auction_data']['buy-now'])) {
                $product = $session_data['data'];
                $buy_now_price = $product->get_buy_now_price();
                $product->set_price($buy_now_price);
                $session_data['data'] = $product;
            }

            return $session_data;

        }

        public function modify_query_loop($q)
        {

            if (is_shop() || is_archive()) {
                if ('no' == get_option('yith_wcact_show_auctions_shop_page')) {
                    $taxquery = $q->get('tax_query');
                    $taxquery[] = array(
                        'taxonomy' => 'product_type',
                        'field' => 'slug',
                        'terms' => 'auction',
                        'operator' => 'NOT IN'
                    );

                    $q->set('tax_query', $taxquery);

                    return;
                }
            }
        }

        /**
         *  Finish auction
         *
         *  If the product_type = 'auction', //The auction end because the user click in buy_now and place order
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function finish_auction($order_id, $post)
        {
            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                $_product = $order->get_product_from_item($item);
                if ('auction' == $_product->get_type()) {
                    $_product->set_stock_status('outofstock');
                    if (!$_product->is_closed()) {
                        yit_save_prop($_product, '_yith_auction_closed_buy_now', 1);
                    }
                }
            }
        }

        /**
         *  yith_check_closed_for_buy_now
         *  Check if auction is closed for buy now
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function check_closed_for_buy_now($status,$product){
            $product = apply_filters('yith_wcact_get_auction_product',$product);

            if ( $product->is_closed_for_buy_now() ) {
                return false;
            }
            return $status;
        }
        /**
         *  Show auction info
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.14
         */
        public function check_if_max_bid_and_reserve_price($product) {
            $args = array(
                'product' =>$product
            );
            wc_get_template('max-bidder.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
        }
        /**
         *  Show auction timeleft
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.14
         */
        public function add_auction_timeleft( $product ) {
            $args = array(
                'product' =>$product,
            );
            wc_get_template('auction-timeleft.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
        }
        /**
         *  Show reserve price and overtime info
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function if_reserve_price($product) {
            $args = array(
                'product' =>$product
            );
            wc_get_template('reserve_price_and_overtime.php', $args, '', YITH_WCACT_TEMPLATE_PATH . 'frontend/');
        }
        
        public function add_button_buy_now($product) {

            $buy_now = yit_get_prop( $product, '_yith_auction_buy_now', true );
            if ( !!$buy_now && $buy_now > 0 ) {
                ?>
                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() );?>"/>
                <button type="submit" class="auction_add_to_cart_button button alt" id="yith-wcact-auction-add-to-cart-button">
                    <?php echo sprintf( _x( 'Buy now for %s','Purchase it now for $ 50.00', 'yith-auctions-for-woocommerce' ), wc_price( $buy_now ) ) ?>
                </button>
                <?php
            }
        }

        public function auction_end($product){

            $instance = YITH_Auctions()->bids;
            $max_bid = $instance->get_max_bid($product->get_id());

            if ( !($product->has_reserve_price() && $product->get_price() < $product->get_reserve_price()) && $max_bid  ) { //Admin email{ //Then we send the email to the winner with the button for paying the order.

                $current_user = wp_get_current_user();
                if ( $current_user->ID == $max_bid->user_id){

                    ?>
                    <div id="Congratulations">
                        <h2><?php _e('ขอแสดงความยินดี คุณคือผู้ชนะการประมูล','yith-auctions-for-woocommerce') ?></h2>
                    </div>
                    <form class="cart" method="get" enctype='multipart/form-data'>
                        <input type="hidden" name="yith-wcact-pay-won-auction" value="<?php echo esc_attr( $product->get_id() );?>"/>
                        <?php
                        if (!$product->is_paid() && ('yes' == get_option('yith_wcact_settings_tab_auction_show_button_pay_now'))) {
                            ?>

                            <?php
                            if('yes' == get_option('yith_wcact_settings_tab_auction_show_add_to_cart_in_auction_product')){

                                ?>
                                <button type="submit" class="auction_add_to_cart_button button alt"
                                        id="yith-wcact-auction-won-auction">
                                    <?php echo sprintf(__('เพิ่มในรถเข็น', 'yith-auctions-for-woocommerce')); ?>
                                </button>
                                <?php

                            } else {
                                ?>
                                <a href="http://localhost/websiteoffice/checkout/"><button type="submit" class="auction_add_to_cart_button button alt"
                                        id="yith-wcact-auction-won-auction" onclick="window.location.href='http://localhost/websiteoffice/checkout/">
                                    <?php echo sprintf(__('ชำระเดี๋ยวนี่', 'yith-auctions-for-woocommerce')); ?>
                                </button></a>
                                <?php
                            }
                        }
                        ?>
                    </form>
                    <?php
                }
            }
        }

        /**
         *  Show form to subscribe to this auction product
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function add_watch_list_button($product) {

            if('yes' == get_option('yith_wcact_settings_tab_auction_allow_subscribe')) {
                $display_watchlist = true;
                $current_user_id = get_current_user_id();
                if( $current_user_id ) {
                    $customer = get_userdata($current_user_id);
                    if($product->is_in_watchlist($customer->data->user_email)) {
                        $display_watchlist = false;
                    }
                }
                if ( $display_watchlist ) {
                    ?>
                    <div class="yith-wcact-watchlist-button">
                        <form class="yith-wcact-watchlist" method="post" enctype='multipart/form-data'>
                            <div class="yith-wcact-watchlist-button">
                                <input type="hidden" name="yith-wcact-auction-id" value="<?php echo esc_attr( $product->get_id() );?>"/>
                                <p><?php _e('Notify me by email when the auction is about to end', 'yith-auctions-for-woocommerce'); ?></p>
                                <input type="email" name="yith-wcact-watchlist-input-email" id="yith-wcact-watchlist-email" value="<?php echo ( $current_user_id ) ? $customer->data->user_email: ''; ?>"
                                       placeholder="<?php _e('Your email', 'yith-auctions-for-woocommerce') ?>">
                                <input type="submit" class="button button-primary yith-wcact-watchlist"
                                       value="<?php _e('Subscribe', 'yith-auctions-for-woocommerce'); ?>">
                            </div>
                        </form>
                    </div>
                    <?php
                }
            }

        }

        /**
         *  Validate email and insert into product watchlist
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function add_to_watchlist()
        {
            if (isset($_REQUEST['yith-wcact-watchlist-input-email'])) {
                $email = $_REQUEST['yith-wcact-watchlist-input-email'];
                if( 0 == strlen($email)){
                    wc_add_notice(sprintf( __('The required email field is empty.',
                        'yith-auctions-for-woocommerce')), 'error');


                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    wc_add_notice(sprintf( __('The format of the email address entered for the watchlist is not correct.',
                        'yith-auctions-for-woocommerce') ), 'error');

                } else {
                    $product_id = $_REQUEST['yith-wcact-auction-id'];
                    $product = wc_get_product($product_id);
                    if (!$product->is_in_watchlist($email)) {
                        $product->set_watchlist($email);
                        wc_add_notice(sprintf( __('Your email "%s" was successfully added to the watchlist.', 'yith-auctions-for-woocommerce'),
                            $email), 'success');
                    }
                }
            }
        }
        
    }
}