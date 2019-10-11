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
 * @class      YITH_Auctions_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_Auction_Admin_Premium' ) ) {
    /**
     * Class YITH_Auctions_Admin
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Auction_Admin_Premium extends YITH_Auction_Admin
    {
        
        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            /* === Register Panel Settings === */
            $this->show_premium_landing = false;

            add_filter('yith_wcact_admin_tabs',array($this,'yith_add_tabs'));
            add_filter('yith_wcact_settings_options',array($this,'yith_add_data_panel_settings_options'));

            add_action('woocommerce_admin_field_yith_wcact_html', array($this, 'yith_regenerate_prices'));

            add_action('yith_before_auction_tab',array($this,'yith_before_auction_tab'));
            add_action('yith_after_auction_tab',array($this,'yith_after_auction_tab'));

            add_action('widgets_init', array($this, 'widgets_init'));

            if ( isset($_REQUEST['yith-wcact-action']) && 'regenerate_auction_prices' === $_REQUEST['yith-wcact-action'] ) {
                add_action('init', array($this, 'regenerate_auction_prices'));
            }

            /* Register plugin to licence/update system */
            add_action('wp_loaded', array($this, 'register_plugin_for_activation'), 99);
            add_action('admin_init', array($this, 'register_plugin_for_updates'));
            
            // Save data product
            add_action('woocommerce_process_product_meta_' . self::$prod_type, array($this, 'save_auction_data'));

            //product columns
            add_action('yith_wcact_render_product_columns_auction_status', array($this, 'render_product_columns_premium'), 10, 3);

            add_action('pre_get_posts', array($this, 'auction_orderby'));

            add_filter('manage_edit-product_sortable_columns', array($this, 'product_sorteable_columns'));

            add_action('restrict_manage_posts', array($this, 'add_post_formats_filter'));
            add_action('pre_get_posts', array($this, 'filter_by_auction_status'));

            add_action( 'add_meta_boxes', array( $this, 'admin_list_bid' ), 30 );

            /*Duplicate products*/
            add_action('woocommerce_duplicate_product', array($this,'duplicate_products'),10,2);

            parent::__construct();
        }

        /**
         * yith add tabs
         *
         * Add tab Appearance in yith admin settings
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return array
         */
        public function yith_add_tabs($tabs) {
            $tabs['appearance'] = __('Appearance', 'yith-auctions-for-woocommerce');
            return $tabs;
        }

        /**
         * yith_add_data_panel_settings_options
         *
         * Add premium options in setings tab
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.11
         * @return array
         */
        public function yith_add_data_panel_settings_options($panel) {
            $regenerate_price_url = '';

            $panel_premium = array(
                /* General settings */
                'settings_options_start'    => array(
                    'type' => 'sectionstart',
                    'id'   => 'yith_wcact_settings_options_start'
                ),

                'settings_options_title'    => array(
                    'title' => _x( 'General settings••A•L•L•4•S•H•A•R•E•.•N•E•T••', 'Panel: page title', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith_wcact_settings_options_title'
                ),

                'settings_show_auctions_shop_page' => array(
                    'title'   => _x( 'Show auctions on shop page', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to show auctions on shop page', 'Admin option description: Check this option to show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_show_auctions_shop_page',
                    'default' => 'yes'
                ),
                'settings_hide_auctions_out_of_stock' => array(
                    'title'   => _x( 'Hide out-of-stock auctions', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to hide out-of-stock auctions on shop page', 'Admin option description: Check this option to show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_hide_auctions_out_of_stock',
                    'default' => 'no'
                ),

                'settings_hide_auctions_closed' => array(
                    'title'   => _x( 'Hide closed auctions', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to hide closed auctions on shop page', 'Admin option description: Check this option to show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_hide_auctions_closed',
                    'default' => 'no'
                ),
                'settings_hide_auctions_not_started' => array(
                    'title'   => _x( 'Hide not started auctions', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to hide not started auctions on shop page', 'Admin option description: Check this option to show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_hide_auctions_not_started',
                    'default' => 'no'
                ),

                'settings_tab_auction_show_button_add_to_cart_instead_of_pay_now' => array(
                    'title'   => _x( 'Possibility to add auction product to cart ', 'Admin option: Posibility to add to cart auction product', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to allow adding auction product and other products to cart in the same order', 'Admin option description: Check this option to show pay now button in product when the auction ends', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_tab_auction_show_add_to_cart_in_auction_product',
                    'default' => 'no'
                ),

                'settings_options_end'      => array(
                    'type' => 'sectionend',
                    'id'   => 'yith_wcact_settings_options_end'
                ),
            );

            $panel_premium = array_merge($panel_premium,$panel);

            $panel_premium2 = array(
                /* Cron settings */

                'settings_cron_auction_options_start'    => array(
                    'type' => 'sectionstart',
                    'id'   => 'yith_wcact_settings_cron_auction_start'
                ),

                'settings_cron_auction_options_title'    => array(
                    'title' => _x( 'Cron settings', 'Panel: page title', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith_wcact_settings_cron_auction_title'
                ),

                'settings_cron_auction_send_emails' => array(
                    'title'   => _x( 'Send emails', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to send emails before auctions end', 'Admin option description: Check this option to show full Username in bid tab', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_cron_auction_send_emails',
                    'default' => 'no'
                ),
                'settings_cron_auction_number_days' => array(
                    'title'   => _x( 'Number of days/hours', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'number',
                    'desc'    => _x( 'Number of days/hours before auction end date for notification', 'Admin option description: number of days', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_cron_auction_number_days',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '1'
                    ),
                    'default'           => '1'
                ),
                'settings_cron_auction_type_numbers' => array(
                    'title'   => _x( 'Select unit', 'Admin option: Select days/hours/minutes', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcact_settings_cron_auction_type_numbers',
                    'options' => array(
                        'days' => _x('days','Admin option: days','yith-auctions-for-woocommerce'),
                        'hours'  => _x('hours','Admin option: hours','yith-auctions-for-woocommerce'),
                        'minutes' => _x('minutes','Admin option: hours','yith-auctions-for-woocommerce')
                    ),
                    'default' => 'days',
                ),
                'settings_tab_auction_show_watchlist' => array(
                    'title'   => _x( 'Allow subscribe auction', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'desc'    => _x( 'Check this option to allow users to subscribe to an auction and be notified when it is about to end', 'Admin option description: Check this option to allow users to subscribe to an auction and be notified when it is about to end', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_tab_auction_allow_subscribe',
                    'default' => 'no'
                ),
                'settings_cron_auction_options_end'      => array(
                    'type' => 'sectionend',
                    'id'   => 'yith_wcact_settings_cron_auction_end'
                ),
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /* Automatic auction rescheduling */
                'settings_automatic_reschedule_auctions_start'    => array(
                    'type' => 'sectionstart',
                    'id'   => 'yith_wcact_settings_automatic_reschedule_auctions_start'
                ),

                'settings_automatic_reschedule_auctions_title'    => array(
                    'title' => _x( 'Automatic auction rescheduling', 'Panel: page title', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith_wcact_settings_automatic_reschedule_auctions_title'
                ),
                'settings_automatic_reschedule_auctions_number' => array(

                    'title'   => _x( 'Number of days/hours/minutes', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'number',
                    'desc'    => _x( 'Number of days/hours/minutes to reschedule auction without bids automatically ', 'Admin option description: number of days', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_automatic_reschedule_auctions_number',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    ),
                    'default'           => '0'
                ),
                'settings_automatic_reschedule_auctions_unit' => array(
                    'title'   => _x( 'Select unit for automatic rescheduling', 'Admin option: Select days/hours/minutes', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcact_settings_automatic_reschedule_auctions_unit',
                    'options' => array(
                        'days' => _x('days','Admin option: days','yith-auctions-for-woocommerce'),
                        'hours'  => _x('hours','Admin option: hours','yith-auctions-for-woocommerce'),
                        'minutes' => _x('minutes','Admin option: hours','yith-auctions-for-woocommerce')
                    ),
                    'default' => 'days',
                ),

                'settings_automatic_reschedule_auctions_end'      => array(
                    'type' => 'sectionend',
                    'id'   => 'yith_wcact_settings_automatic_reschedule_auctions_end'
                ),

                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /* Overtime settings */
                'settings_overtime_auction_options_start'    => array(
                    'type' => 'sectionstart',
                    'id'   => 'yith_wcact_settings_overtime_auction_start'
                ),
                'settings_overtime_auction_title'    => array(
                    'title' => _x( 'Overtime settings', 'Panel: page title', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith_wcact_settings_overtime_auction_title'
                ),
                'settings_overtime_auction_number_minutes' => array(
                    'title'   => _x( 'Time to add overtime', 'Admin option: Time to add overtime', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'number',
                    'desc'    => _x( 'Number of minutes before auction ends to check if added overtime', 'Admin option description: Number of minutes before auction ends to check if added overtime', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_overtime_option',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    ),
                    'default'           => '0'
                ),
                'settings_overtime_auction_overtime' => array(
                    'title'   => _x( 'Overtime', 'Admin option: Overtime', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'number',
                    'desc'    => _x( 'Number of minutes for which the auction will be extended', 'Admin option description: Number of minutes for which the auction will be extended', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_overtime',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    ),
                    'default'           => '0'
                ),
                'settings_overtime_auction_options_end'      => array(
                    'type' => 'sectionend',
                    'id'   => 'yith_wcact_settings_overtime_auction_end'
                ),
                ////////////////////////////////////////////////////////////////////////////////////////////////////////////
                /* Regenerate auction prices */
                'settings_regenerate_auction_options_start'    => array(
                    'type' => 'sectionstart',
                    'id'   => 'yith_wcact_settings_regenerate_auction_start'
                ),

                'settings_regenerate_auction_options_title'    => array(
                    'title' => _x( 'Regenerate auction prices', 'Panel: page title', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith_wcact_settings_regenerate_auction_title'
                ),
                'settings_regenerate_auction_options_button' => array(
                    'title' => __( 'Regenerate auction prices', 'yith-auctions-for-woocommerce' ),
                    'id'    => 'yith_wcact_settings_regenerate_auction_button',
                    'desc'  => __( 'Click this button to regenerate all auction prices', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'yith_wcact_html',
                    'html'  => '<a class="button" href="'.$regenerate_price_url.'">'.__( 'Regenerate auction prices', 'yith-auctions-for-woocommerce' ).'</a>',
                ),
                'settings_regenerate_auction_options_end'      => array(
                    'type' => 'sectionend',
                    'id'   => 'yith_wcact_settings_regenerate_auction_end'
                ),
                ///////////////////////////////////////////////////////////////////////////////////////////////////////////
                /* Live auctions */
                'settings_live_auction_options_start'    => array(
                    'type' => 'sectionstart',
                    'id'   => 'yith_wcact_settings_live_auction_start'
                ),

                'settings_live_auction_options_title'    => array(
                    'title' => _x( 'Live Auctions', 'Panel: page title', 'yith-auctions-for-woocommerce' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith_wcact_settings_live_auction_title'
                ),
                'settings_live_auction_my_auctions' => array(
                    'title'   => _x( 'Live auctions on My auctions', 'Admin option: Overtime', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'number',
                    'desc'    => _x( 'Seconds to pass before checking new auction changes on My account > My auctions. Set to "0" to disable live auction on My auctions', 'Admin option description: Seconds to pass before checking new auction changes on My account > My auctions. Set to "0" to disable live auction on My auctions', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_live_auction_my_auctions',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    ),
                    'default'           => '0'
                ),
                'settings_live_auction_product_page' => array(
                    'title'   => _x( 'Live auctions on auction product page', 'Admin option: Live auctions on auction product page', 'yith-auctions-for-woocommerce' ),
                    'type'    => 'number',
                    'desc'    => _x( 'Seconds to pass before checking new auction changes on auction product page. Set to "0" to disable live auction on product page', 'Admin option description: Seconds to pass before checking new auction changes on auction product page. Set to "0" to disable live auction on product page', 'yith-auctions-for-woocommerce' ),
                    'id'      => 'yith_wcact_settings_live_auction_product_page',
                    'custom_attributes' => array(
                        'step' => '1',
                        'min'  => '0'
                    ),
                    'default'           => '0'
                ),
                'settings_live_auction_options_end'      => array(
                    'type' => 'sectionend',
                    'id'   => 'yith_wcact_settings_regenerate_auction_end'
                ),
            );
            $panel_premium2 = array_merge($panel_premium,$panel_premium2);
            return $panel_premium2;
        }

        /**
         * YITH before auction tab
         *
         * Add input in auction tab
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.11
         */

        public function yith_before_auction_tab($post_id) {
            $product = wc_get_product($post_id);
            woocommerce_wp_text_input( array(
                'id'                => '_yith_auction_start_price',
                'name'              => '_yith_auction_start_price',
                'class'             => 'wc_input_price short',
                'label'             => __( 'Starting Price', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'value'             => yit_get_prop( $product, '_yith_auction_start_price', true ),
                'data_type'         => 'price',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            ) );

            woocommerce_wp_text_input( array(
                'id'                => '_yith_auction_bid_increment',
                'name'              => '_yith_auction_bid_increment',
                'class'             => 'wc_input_price short',
                'label'             => __( 'Bid up', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'value'             => yit_get_prop( $product, '_yith_auction_bid_increment', true ),
                'data_type'         => 'price',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            ) );

            woocommerce_wp_text_input( array(
                'id'                => '_yith_auction_minimum_increment_amount',
                'name'              => '_yith_auction_minimum_increment_amount',
                'class'             => 'wc_input_price short',
                'label'             => __( 'Minimum increment amount', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'value'             => yit_get_prop( $product, '_yith_auction_minimum_increment_amount', true ),
                'desc_tip'      => 'true',
                'description'   => __( 'Minimum amount to increase manual bids', 'yith-auctions-for-woocommerce' ),
                'data_type'         => 'price',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            ) );

            woocommerce_wp_text_input( array(
                'id'                => '_yith_auction_reserve_price',
                'name'              => '_yith_auction_reserve_price',
                'class'             => 'wc_input_price short',
                'label'             => __( 'Reserve price', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'value'             => yit_get_prop( $product, '_yith_auction_reserve_price', true ),
                'data_type'         => 'price',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            ) );

            woocommerce_wp_text_input( array(
                'id'                => '_yith_auction_buy_now',
                'name'              => '_yith_auction_buy_now',
                'class'             => 'wc_input_price short',
                'label'             => __( 'Buy it now price', 'yith-auctions-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'value'             => yit_get_prop( $product, '_yith_auction_buy_now', true ),
                'data_type'         => 'price',
                'custom_attributes' => array(
                    'step' => 'any',
                    'min'  => '0'
                )
            ) );

        }

        /**
         * YITH after auction tab
         *
         * Add input in auction tab
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.11
         */

        public function yith_after_auction_tab($post_id) {

            $product = wc_get_product($post_id);
            woocommerce_wp_text_input(array(
                'id' => '_yith_check_time_for_overtime_option',
                'name' => '_yith_check_time_for_overtime_option',
                'class' => 'wc_input_price short',
                'label' => __('Time to add overtime', 'yith-auctions-for-woocommerce'),
                'value' => yit_get_prop($product, '_yith_check_time_for_overtime_option', true),
                'data_type' => 'decimal',
                'description' => __('Number of minutes before auction ends to check if overtime added. (Override the settings option)', 'yith-auctions-for-woocommerce'),
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '0'
                )
            ));

            woocommerce_wp_text_input(array(
                'id' => '_yith_overtime_option',
                'name' => '_yith_overtime_option',
                'class' => 'wc_input_price short',
                'label' => __('Overtime', 'yith-auctions-for-woocommerce'),
                'value' => yit_get_prop($product, '_yith_overtime_option', true),
                'data_type' => 'decimal',
                'description' => __('Number of minutes by which the auction will be extended. (Overrride the settings option)', 'yith-auctions-for-woocommerce'),
                'custom_attributes' => array(
                    'step' => 'any',
                    'min' => '0'
                )
            ));

            /*Automatic re-schedule*/
            woocommerce_wp_text_input( array(
                'id'                => '_yith_wcact_auction_automatic_reschedule',
                'value'             => yit_get_prop($product,'_yith_wcact_auction_automatic_reschedule',true),
                'label'             => _x( 'Time value for automatic rescheduling', 'Admin option: Show auctions on shop page', 'yith-auctions-for-woocommerce' ),
                'desc_tip'          => true,
                'description'       => _x( 'Number of days/hours/minutes to reschedule auction without bids automatically (Override the settings option)', 'Admin option description: number of days. (Override the settings option)', 'yith-auctions-for-woocommerce' ),
                'custom_attributes' => array(
                    'step'          => 'any',
                    'min'           => '0',
                ),
                'data_type'         => 'decimal',
            ) );

            woocommerce_wp_select(array(
                'id' => '_yith_wcact_automatic_reschedule_auction_unit',
                'name' => '_yith_wcact_automatic_reschedule_auction_unit',
                'label' => _x( 'Select unit for automatic rescheduling', 'Admin option: Select days/hours/minutes', 'yith-auctions-for-woocommerce' ),
                'options' => array(
                    'days' => _x('days','Admin option: days','yith-auctions-for-woocommerce'),
                    'hours'  => _x('hours','Admin option: hours','yith-auctions-for-woocommerce'),
                    'minutes' => _x('minutes','Admin option: hours','yith-auctions-for-woocommerce')
                ),
                'value' => yit_get_prop($product,'_yith_wcact_automatic_reschedule_auction_unit',true,'edit'),
            ));


            woocommerce_wp_checkbox(
                array(
                    'id'            => '_yith_wcact_show_upbid',
                    'label'         => __('Show bid up', 'yith-auctions-for-woocommerce' ),
                    'desc_tip'      => 'true',
                    'description'   => __( 'Check this option to show Bid up on product page', 'yith-auctions-for-woocommerce' ),
                    'value'         => yit_get_prop( $product, '_yith_wcact_upbid_checkbox', true ),
                )
            );

            woocommerce_wp_checkbox(
                array(
                    'id'            => '_yith_wcact_show_overtime',
                    'label'         => __('Show overtime', 'yith-auctions-for-woocommerce' ),
                    'desc_tip'      => 'true',
                    'description'   => __( 'Check this option to show overtime on product page', 'yith-auctions-for-woocommerce' ),
                    'value'         => yit_get_prop( $product, '_yith_wcact_overtime_checkbox', true ),
                )
            );




            if($product && 'auction' == $product->get_type() && ($product->is_closed() ||  yit_get_prop($product,'stock_status',true) == 'outofstock' ) ) {
                echo '<div id="yith-reshedule">';
                echo '<p class="form-field wc_auction_reshedule"><input type="button" class="button" id="reshedule_button" value="' . __('Re-schedule', 'yith-auctions-for-woocommerce') . '"></p>';
                echo '<p class="form-field" id="yith-reshedule-notice-admin">' . __(' Change the dates and click on the update button to re-schedule the auction', 'yith-auctions-for-woocommerce') . '</p>';
                echo '</div>';
            }
        }
        /**
         * Save the data input into the auction product box
         *
         * @author   Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since    1.0.11
         */
        public function save_auction_data($post_id)
        {
            parent::save_auction_data($post_id);
            $product = wc_get_product($post_id);
            $product_type = empty($_POST['product-type']) ? 'simple' : sanitize_title(stripslashes($_POST['product-type']));

            if ('auction' == $product_type) {

                $bids = YITH_Auctions()->bids;
                $exist_auctions = $bids->get_max_bid($post_id);

                if (isset($_POST['_yith_auction_start_price']) && $_POST['_yith_auction_start_price'] >= 0) {
                    if (!$exist_auctions) {
                        yit_save_prop($product, '_yith_auction_start_price', wc_format_decimal(wc_clean($_POST['_yith_auction_start_price'])));
                    }
                }

                if (isset($_POST['_yith_auction_bid_increment']) && $_POST['_yith_auction_bid_increment'] >= 0) {
                    yit_save_prop($product, '_yith_auction_bid_increment', wc_format_decimal(wc_clean($_POST['_yith_auction_bid_increment'])));
                }

                if (isset($_POST['_yith_auction_minimum_increment_amount']) && $_POST['_yith_auction_minimum_increment_amount'] >= 0) {
                    yit_save_prop($product, '_yith_auction_minimum_increment_amount', wc_format_decimal(wc_clean($_POST['_yith_auction_minimum_increment_amount'])));
                }


                if (isset($_POST['_yith_auction_reserve_price']) && $_POST['_yith_auction_reserve_price'] >= 0) {
                    yit_save_prop($product, '_yith_auction_reserve_price', wc_format_decimal(wc_clean($_POST['_yith_auction_reserve_price'])));
                }

                if (isset($_POST['_yith_auction_buy_now']) && $_POST['_yith_auction_buy_now'] >= 0) {
                    yit_save_prop($product, '_yith_auction_buy_now', wc_format_decimal(wc_clean($_POST['_yith_auction_buy_now'])));
                }
                
                if (isset($_POST['_yith_auction_to'])) {
                    //Clear all Product CronJob
                    if (wp_next_scheduled('yith_wcact_send_emails', array($post_id))) {
                        wp_clear_scheduled_hook('yith_wcact_send_emails', array($post_id));
                    }
                    //Create the CronJob //when the auction is about to end
                    do_action('yith_wcact_register_cron_email', $post_id);

                    //Clear all Product CronJob
                    if (wp_next_scheduled('yith_wcact_send_emails_auction', array($post_id))) {
                        wp_clear_scheduled_hook('yith_wcact_send_emails_auction', array($post_id));
                    }
                    //Create the CronJob //when the auction end, winner and vendors
                    do_action('yith_wcact_register_cron_email_auction', $post_id);

                }

                if (isset($_POST['_yith_check_time_for_overtime_option'])&& $_POST['_yith_check_time_for_overtime_option'] >= 0) {
                    yit_save_prop($product, '_yith_check_time_for_overtime_option', wc_format_decimal(wc_clean($_POST['_yith_check_time_for_overtime_option'])));
                }

                if (isset($_POST['_yith_overtime_option'])&& $_POST['_yith_overtime_option'] >= 0) {
                    yit_save_prop($product, '_yith_overtime_option', wc_format_decimal(wc_clean($_POST['_yith_overtime_option'])));
                }

                if(isset($_POST['_yith_wcact_auction_automatic_reschedule'] ) && $_POST['_yith_wcact_auction_automatic_reschedule'] >=0 ) {
                    yit_save_prop($product, '_yith_wcact_auction_automatic_reschedule', wc_format_decimal(wc_clean($_POST['_yith_wcact_auction_automatic_reschedule'])),true);
                }

                if(isset($_POST['_yith_wcact_automatic_reschedule_auction_unit'] )) {
                    yit_save_prop($product, '_yith_wcact_automatic_reschedule_auction_unit', $_POST['_yith_wcact_automatic_reschedule_auction_unit'],true);
                }

                $show_bidup = isset($_POST['_yith_wcact_show_upbid']) ? 'yes' : 'no';
                yit_save_prop($product, '_yith_wcact_upbid_checkbox', $show_bidup);

                $show_overtime = isset($_POST['_yith_wcact_show_overtime']) ? 'yes' : 'no';
                yit_save_prop($product, '_yith_wcact_overtime_checkbox', $show_overtime );
                

                //Prevent issues with orderby in shop loop
                if (!$exist_auctions) {
                    yit_save_prop($product, '_price',$_POST['_yith_auction_start_price']);
                }
            }
        }

        /**
         * render products columns
         *
         * Add content to Start date and Close date
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.11
         * @return void
         */
        public function render_product_columns_premium($column, $type,$product)
        {
            if($column == 'yith_auction_status') {
                switch ($type) {
                    case 'started-reached-reserve' :
                        echo '<span class="yith-wcact-auction-status yith-auction-started-reached-reserve tips" data-tip="' . esc_attr__('Started and not exceeded the reserve price', 'yith-auctions-for-woocommerce') . '">';
                        break;
                    case 'finished-reached-reserve' :
                        echo '<span class="yith-wcact-auction-status yith-auction-finished-reached-reserve tips" data-tip="' . esc_attr__('Finished and not exceeded the reserve price', 'yith-auctions-for-woocommerce') . '"></span>';
                        break;
                    case 'finnish-buy-now' :
                        echo '<span class="yith-wcact-auction-status yith-auction-finnish-buy-now tips" data-tip="' . esc_attr__('Purchased through buy-now', 'yith-auctions-for-woocommerce') . '"></span>';
                        break;
                }
            }
        }


        /**
         * Auction Order By
         *
         * Order by start date or end date in datatable products
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function auction_orderby($query)
        {

            if (!is_admin())
                return;

            $orderby = $query->get('orderby');

            switch ($orderby) {
                case 'yith_auction_start_date':
                    $query->set('meta_key', '_yith_auction_for');
                    $query->set('orderby', 'meta_value');
                    break;
                case 'yith_auction_end_date':
                    $query->set('meta_key', '_yith_auction_to');
                    $query->set('orderby', 'meta_value');
                    break;
            }
        }

        /**
         * Sorteable columns
         *
         * convert "Start Date" and "End Date" in sorteable columns product datatable
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function product_sorteable_columns($columns)
        {
            $columns['yith_auction_start_date'] = 'yith_auction_start_date';
            $columns['yith_auction_end_date'] = 'yith_auction_end_date';

            return $columns;
        }


        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once YITH_WCACT_PATH . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_WCACT_PATH . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register(YITH_WCACT_INIT, YITH_WCACT_SECRETKEY, YITH_WCACT_SLUG);

        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if (!class_exists('YIT_Upgrade')) {
                require_once(YITH_WCACT_PATH . '/plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register(YITH_WCACT_SLUG, YITH_WCACT_INIT);
        }

        /**
         * Add new filter in product tab
         *
         * @return void
         * @since    1.0.7
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function add_post_formats_filter($post_type)
        {
            if ($post_type == 'product') {
                $output = '<select name="auction_type" id="dropdown_auction_type">';
                $output .= '<option value="">' . __('Show all auction statuses', 'yith-auctions-for-woocommerce') . '</option>';
                $output .= '<option value="non-started">' . __('Not Started', 'yith-auctions-for-woocommerce') . '</option>';
                $output .= '<option value="started">' . __('Started', 'yith-auctions-for-woocommerce') . '</option>';
                $output .= '<option value="finished">' . __('Finished', 'yith-auctions-for-woocommerce') . '</option>';
                $output .= '</option>';
                $output .= '</select>';
                echo apply_filters('yith_wcact_woocommerce_auction_filters', $output);
            }
        }

        /**
         * Filter by auction status
         *
         * @return void
         * @since    1.0.7
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function filter_by_auction_status($query)
        {
            global $post_type;
            if (!is_admin())
                return;

            if ($post_type == 'product') {

                if (isset($_GET['auction_type'])) {

                    $orderby = $_GET['auction_type'];
                    $product_type = $query->get('product_type');
                    switch ($orderby) {
                        case 'non-started' :
                            $query->set('meta_query', array(
                                array(
                                    'key' => '_yith_auction_for',
                                    'value' => strtotime('now'),
                                    'compare' => '>'
                                )
                            ));
                            break;
                        case 'started' :
                            $query->set('meta_query', array(
                                'relation' => 'AND',
                                array(
                                    'key' => '_yith_auction_for',
                                    'value' => strtotime('now'),
                                    'compare' => '<'
                                ),
                                array(
                                    'key' => '_yith_auction_to',
                                    'value' => strtotime('now'),
                                    'compare' => '>'
                                )
                            ));

                            break;
                        case 'finished' :
                            $query->set('meta_query', array(
                                array(
                                    'key' => '_yith_auction_to',
                                    'value' => strtotime('now'),
                                    'compare' => '<'
                                )
                            ));
                            break;
                    }
                }
            }
        }
        
        /**
         * Create metabox with list of bid for each product
         *
         * @return void
         * @since    1.0.14
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function admin_list_bid($post_type) {
            $post_types = array('product');     //limit meta box to certain post types
            global $post;
            $product = wc_get_product( $post->ID );
            if ( in_array( $post_type, $post_types ) && ($product->get_type() == 'auction' ) ) {
                add_meta_box('yith-wcgpf-auction-bid-list', __('Auction bid list', 'yith-auctions-for-woocommerce'), array($this, 'auction_bid_list'), $post_type, 'normal', 'low');
            }
        }
        function auction_bid_list($post) {
            $args = array(
                'post_id' => $post->ID
            );
            wc_get_template('admin-list-bids.php', $args , '', YITH_WCACT_TEMPLATE_PATH . 'admin/');
        }

        /**
         * Control overtime product
         *
         * @return void
         * @since    1.0.14
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function duplicate_products($new_id,$post_id) {
            $product_new = wc_get_product($new_id);
            $is_in_overtime = yit_get_prop($product_new,'_yith_is_in_overtime',true);
            if ( $is_in_overtime ) {
                yit_delete_prop($product_new,'_yith_is_in_overtime',false);
            }
        }

    }
}