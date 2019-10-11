<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCACT_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_AUCTIONS
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'WC_Product_Auction_Premium' ) ) {
	/**
	 * Class WC_Product_Auction_Premium
	 *
	 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
	 */
	class WC_Product_Auction_Premium extends WC_Product_Auction
    {
        /**
         * Constructor gets the post object and sets the ID for the loaded product.
         *
         * @param int|WC_Product|object $product Product ID, post object, or product object
         */

        protected $status = false;

        public function __construct($product)
        {

            parent::__construct($product);
        }


        public function get_current_bid()
        {
            $bids = YITH_Auctions()->bids;
            $current_bid = yit_get_prop($this, '_yith_auction_start_price');
            $bid_increment = $this->get_bid_increment();
            $reserve_price = $this->get_reserve_price();
            $buy_now = yit_get_prop($this, '_yith_auction_closed_buy_now', true);

            if (!$buy_now) {

                if ($bid_increment > 0) {
                    /*-------WITH BID INCREMENT---------*/
                    $last_two_bids = $bids->get_last_two_bids($this->get_id());

                    if (count($last_two_bids) == 2) {

                        // I have two or more bids
                        $first_bid = $last_two_bids[0] && isset($last_two_bids[0]->bid) ? $last_two_bids[0]->bid : 0;
                        $second_bid = $last_two_bids[1] && isset($last_two_bids[1]->bid) ? $last_two_bids[1]->bid : 0;

                        if ($first_bid == $second_bid) {

                            $current_bid = max(yit_get_prop($this, '_yith_auction_start_price'), $first_bid);

                        } else {
                            $is_auto_bid = ($first_bid - $second_bid) > $bid_increment;

                            if ($first_bid >= $reserve_price && $second_bid < $reserve_price) {

                                $current_bid = $reserve_price;

                            } elseif ($is_auto_bid) {

                                $current_bid = max(yit_get_prop($this, '_yith_auction_start_price'), $second_bid + $bid_increment);

                            } else {

                                $current_bid = max(yit_get_prop($this, '_yith_auction_start_price'), $first_bid);
                            }
                        }
                    } elseif (count($last_two_bids) == 1) {
                        // I have only one bid
                        $the_bid = $last_two_bids[0];

                        if ($the_bid && isset($the_bid->bid) && $the_bid->bid >= yit_get_prop($this, '_yith_auction_start_price')) {
                            if ($the_bid->bid >= $reserve_price && isset($reserve_price) && $reserve_price > 0) {
                                $current_bid = $reserve_price;

                            } elseif (yit_get_prop($this, '_yith_auction_start_price') == 0) {

                                $current_bid = $this->get_bid_increment();

                            } else {
                                $current_bid = yit_get_prop($this, '_yith_auction_start_price');

                            }
                        }
                    }
                } else {
                    /*-------WITHOUT BID INCREMENT---------*/
                    $max_bid = $bids->get_max_bid($this->get_id());

                    if ($max_bid && isset($max_bid->bid) && $max_bid->bid >= yit_get_prop($this, '_yith_auction_start_price')) {

                        $current_bid = $max_bid->bid;
                    }
                }
            } else {

                $current_bid = $this->get_buy_now_price();

            }
            $the_current_bid = apply_filters('yith_wcact_get_current_bid', $current_bid, $this);
            yit_set_prop($this, 'current_bid', $the_current_bid);

            return $the_current_bid;
        }

        /**
         *  Get bid increment of the product.
         *
         */
        public function get_bid_increment()
        {
            $bid_increment = yit_get_prop($this, '_yith_auction_bid_increment');
            return isset($bid_increment) ? $bid_increment : false;
        }

        /**
         *  Get minimum manual bid increment of the product.
         *
         */
        
        public function get_minimum_manual_bid_increment()
        {
            $minimum_manual_bid = yit_get_prop($this, '_yith_auction_minimum_increment_amount');
            return isset($minimum_manual_bid) ? $minimum_manual_bid : false;
        }
        /**
         *  Get buy now price of the product.
         *
         */
        public function get_buy_now_price()
        {
            $buy_now = yit_get_prop($this, '_yith_auction_buy_now');
            return isset($buy_now) ? $buy_now : false;
        }


        /**
         *  Get reserve price of the product.
         *
         */
        public function get_reserve_price()
        {
            $reserve_price = yit_get_prop($this, '_yith_auction_reserve_price');
            return isset($reserve_price) ? $reserve_price : false;
        }


        /**
         * Product has a reserve price
         *
         */

        public function has_reserve_price()
        {
            $reserve_price = yit_get_prop($this, '_yith_auction_reserve_price');

            if (isset($reserve_price) && $reserve_price) {

                return TRUE;

            } else {

                return FALSE;
            }
        }

        /**
         *  Check if the auction is close for user click in buttom buy_now and place order.
         *
         */
        public function is_closed_for_buy_now()
        {
            $closed_buy_now = yit_get_prop($this, '_yith_auction_closed_buy_now');
            if (isset($closed_buy_now) && $closed_buy_now) {

                return TRUE;

            } else {

                return FALSE;
            }
        }

        /**
         *  return status of auction
         *
         */
        public function get_auction_status()
        {

            $instance = YITH_Auctions()->bids;
            $max_bid = $instance->get_max_bid($this->get_id());

            if ($max_bid) {

                $max_bid = $max_bid->bid;

            } else {

                $max_bid = 0;
            }

            if ($this->is_start() && !$this->is_closed()) {

                if ($this->has_reserve_price() && $max_bid < $this->get_reserve_price() && !$this->is_closed_for_buy_now()) {

                    return 'started-reached-reserve';

                } elseif ($this->is_closed_for_buy_now()) {

                    return 'finnish-buy-now';

                } else {

                    return 'started';
                }

            } elseif ($this->is_closed()) {

                if ($this->has_reserve_price() && $max_bid < $this->get_reserve_price()) {

                    return 'finished-reached-reserve';

                } else {

                    return 'finished';
                }

            } else {
                return 'non-started';
            }
        }

        /**
         *  return if the auction product is visible
         *
         */
        public function is_visible()
        {
            if (('yes' == get_option('yith_wcact_hide_auctions_out_of_stock') && $this->is_closed_for_buy_now())) {
                return false;
            }
            if (('yes' == get_option('yith_wcact_hide_auctions_closed') && $this->is_closed())) {
                return false;
            }
            if (('yes' == get_option('yith_wcact_hide_auctions_not_started') && !$this->is_start())) {
                return false;
            }

            return parent::is_visible();
        }

        /**
         *  return global or local check to add overtime
         *
         */
        public function check_for_overtime()
        {
            $check_for_overtime = yit_get_prop($this, '_yith_check_time_for_overtime_option');

            if (isset($check_for_overtime) && $check_for_overtime) {
                return $check_for_overtime;
            } else {
                return get_option('yith_wcact_settings_overtime_option', 0);
            }
        }

        /**
         *  return global or local overtime
         *
         */
        public function get_overtime()
        {
            $overtime = yit_get_prop($this, '_yith_overtime_option');
            if (isset($overtime) && $overtime) {
                return $overtime;
            } else {
                return get_option('yith_wcact_settings_overtime', 0);
            }
        }

        /**
         *  return true if is in overtime
         *
         */
        public function is_in_overtime()
        {
            $is_in_overtime = yit_get_prop($this, '_yith_is_in_overtime');

            if (isset($is_in_overtime) && $is_in_overtime) {

                return TRUE;

            } else {

                return FALSE;
            }
        }

        /**
         *  return automatic reschedule time
         *
         */
        public function get_automatic_reschedule_time()
        {
            $time_quantity = yit_get_prop($this, '_yith_wcact_auction_automatic_reschedule', true);

            if (isset($time_quantity) && $time_quantity >= 0) {
                $time = array(
                    'time_quantity' => $time_quantity,
                    'time_unit' => yit_get_prop($this, '_yith_wcact_automatic_reschedule_auction_unit', true),
                );

            } else {
                $time = array(
                    'time_quantity' => get_option('yith_wcact_settings_automatic_reschedule_auctions_number', 0),
                    'time_unit' => get_option('yith_wcact_settings_automatic_reschedule_auctions_unit', 'minutes')
                );
            }
            return $time;
        }

        /**
         *  return list of watchlist user
         *
         */
        public function get_watchlist()
        {
            $watchlist = yit_get_prop($this, 'yith_wcact_auction_watchlist', true);

            if ( isset( $watchlist ) && $watchlist ) {

                return $watchlist;
            }else {
                return false;
            }
        }

        /**
         *  insert email in watchlist
         *
         */
        public function set_watchlist( $user_email ) {
            $watchlist = yit_get_prop($this, 'yith_wcact_auction_watchlist', true);
            if(!is_array($watchlist)) {
                $watchlist = array();
            }
            $watchlist[] = $user_email;
            yit_save_prop($this,'yith_wcact_auction_watchlist',$watchlist,true);
        }

        /**
         *  return is in watchlist
         *
         */
        public function is_in_watchlist( $user_email ) {
            $watchlist = yit_get_prop($this, 'yith_wcact_auction_watchlist', true);
            if(is_array($watchlist) && in_array($user_email,$watchlist)) {
                return true;
            } else {
                return false;
            }
        }

    }

}




