<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Seller
 */

get_header(); ?>

	<div id="primary" class="content-area col-md-8">
		<div id="home-title">
			<span><?php esc_html_e('Recent Posts','seller'); ?></span>
		</div>
		<main id="main" class="site-main" role="main">
		<?php $count = 0; ?>
		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
                do_action('seller_blog_layout');?>
			<?php endwhile; ?>
			<?php seller_pagination(); ?>

		<?php else : ?>

			<?php get_template_part( 'modules/content/content', 'none' ); ?>

		<?php endif; ?>
		</main><!-- #main -->

	</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>