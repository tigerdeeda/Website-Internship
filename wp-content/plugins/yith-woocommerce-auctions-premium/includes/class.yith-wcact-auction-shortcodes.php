<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if (!defined('YITH_WCACT_PATH')) {
    exit('Direct access forbidden.');
}
/**
 *
 *
 * @class      YITH_Auction_Shortcodes
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if (!class_exists('YITH_WCACT_Auction_Shortcodes')) {
    /**
     * Class YITH_Auction_Shortcodes
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCACT_Auction_Shortcodes
    {

        public static function init()
        {
            $shortcodes = array(
                'yith_auction_products' => __CLASS__ . '::yith_auction_products', // print auction products
            );

            foreach ($shortcodes as $shortcode => $function) {
                add_shortcode($shortcode, $function);
            }
        }

        /**
         * Loop over found products.
         * @param  array $query_args
         * @param  array $atts
         * @param  string $loop_name
         * @return string
         */
        private static function product_loop( $query_args, $atts, $loop_name ) {
            global $woocommerce_loop;

            $products                    = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, $loop_name ) );
            $columns                     = absint( $atts['columns'] );
            $woocommerce_loop['columns'] = $columns;
            $woocommerce_loop['name']    = $loop_name;

            ob_start();
            if ( $products->have_posts() ) {
                ?>

                <?php do_action( "woocommerce_shortcode_before_{$loop_name}_loop" ); ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php do_action( "woocommerce_shortcode_after_{$loop_name}_loop" ); ?>

                <?php
            } else {
                do_action( "woocommerce_shortcode_{$loop_name}_loop_no_results" );
            }

            woocommerce_reset_loop();
            wp_reset_postdata();

            return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
        }


        /**
         * ShortCode for auction products
         *
         * @return void
         * @since 1.0.0
         */
        public static function yith_auction_products($atts)
        {
            $atts = shortcode_atts( array(
                'columns' => '4',
                'orderby' => 'title',
                'order'   => 'asc',
                'ids'     => '',
                'skus'    => ''
            ), $atts, 'products' );

            $query_args = array(
                'post_type'           => 'product',
                'post_status'         => 'publish',
                'ignore_sticky_posts' => 1,
                'orderby'             => $atts['orderby'],
                'order'               => $atts['order'],
                'posts_per_page'      => -1,
                'meta_query'          => WC()->query->get_meta_query()
            );

            if ( $auction_term = get_term_by( 'slug', 'auction', 'product_type' ) ) {
                $posts_in = array_unique((array)get_objects_in_term($auction_term->term_id, 'product_type'));
                if (! empty ( $posts_in)) {

                    $query_args['post__in'] = array_map('trim', $posts_in ) ;

                    // Ignore catalog visibility
                    $query_args['meta_query'] = array_merge($query_args['meta_query'], WC()->query->stock_status_meta_query());

                    wp_enqueue_style('yith-wcact-frontend-css');
                    wp_enqueue_script('yith_wcact_frontend_shop', YITH_WCACT_ASSETS_URL . '/js/frontend_shop.js', array('jquery', 'jquery-ui-sortable'), YITH_WCACT_VERSION, true);
                    wp_localize_script('yith_wcact_frontend_shop', 'object', array(
                        'ajaxurl' => admin_url('admin-ajax.php')
                    ));

                    return self::product_loop( $query_args, $atts, 'yith_auction_products' );
                }
            }
            return '';
        }
    }
}