<?php
/**
 * @package Seller
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('col-md-4 col-sm-6 grid3'); ?>>

		<div class="featured-thumb col-md-12">
			<?php if (has_post_thumbnail()) : ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title() ?>"><?php the_post_thumbnail('grid3',array(  'alt' => trim(strip_tags( $post->post_title )))); ?></a>
			<?php else: ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title() ?>"><img alt = "<?php the_title()?>" src="<?php echo get_template_directory_uri()."/assets/images/placeholder2.jpg"; ?>"></a>
			<?php endif; ?>
		</div><!--.featured-thumb-->
			
		<div class="out-thumb col-md-12">
			<header class="entry-header">
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<span class="entry-excerpt"><?php echo substr(get_the_excerpt(),0,150).(get_the_excerpt() ? "..." : "" ); ?></span>
			</header><!-- .entry-header -->
		</div><!--.out-thumb-->
		
</article><!-- #post-## -->