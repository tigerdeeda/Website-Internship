<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Seller
  <!--?php /*get_template_part('modules/navigation/footer','menu');?>
 */
?>

	</div><!-- #content -->
</div><!-- #page -->

<?php get_template_part('sidebar', 'footer'); ?>

<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="container">
		<div class="site-info col-md-4">
		    <?php printf( __( '', 'seller' ) ); ?>
			<?php echo esc_html(get_theme_mod('seller_footer_text')); ?>
		</div><!-- .site-info -->

       
	</div><!--.container-->
</footer><!-- #colophon -->

<?php wp_footer(); ?>

</body>
</html>