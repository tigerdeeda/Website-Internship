<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Seller
 */

get_header(); ?>
	
	<h2 class="header-title col-md-12">
		<?php
						if ( is_category() ) :
							single_cat_title();

						elseif ( is_tag() ) :
							single_tag_title();

						elseif ( is_author() ) :
							printf( __( 'Author: %s', 'seller' ), get_the_author()  );

						elseif ( is_day() ) :
							printf( __( 'Day: %s', 'seller' ),  get_the_date()  );

						elseif ( is_month() ) :
							printf( __( 'Month: %s', 'seller' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'seller' ) )  );

						elseif ( is_year() ) :
							printf( __( 'Year: %s', 'seller' ),  get_the_date( _x( 'Y', 'yearly archives date format', 'seller' ) ) );

						elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
                            esc_html_e('Asides', 'seller' );

						elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
                            esc_html_e( 'Galleries', 'seller');

						elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
                            esc_html_e( 'Images', 'seller');

						elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
                            esc_html_e( 'Videos', 'seller' );

						elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
                            esc_html_e('Quotes', 'seller' );

						elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
                            esc_html_e( 'Links', 'seller' );

						elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
                            esc_html_e( 'Statuses', 'seller' );

						elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
                            esc_html_e( 'Audios', 'seller' );

						elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
                            esc_html_e( 'Chats', 'seller' );

						else :
                            esc_html_e( 'Archives', 'seller' );

						endif;
					?>
	</h2>
	<section id="primary" class="content-area col-md-8">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>
			<?php $count = 0; ?>
			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post();

                do_action('seller_blog_layout');?>

			<?php endwhile; ?>

			<?php seller_pagination(); ?>

		<?php else : ?>

			<?php get_template_part( 'modules/content/content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->
<?php get_sidebar() ?>
<?php get_footer(); ?>
