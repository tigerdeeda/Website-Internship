<header id="masthead" class="site-header" role="banner">
    <div class="container">
        <div class="site-branding col-lg-4 col-md-12">
            <?php if ( get_theme_mod('seller_logo') != "" ) : ?>
                <div id="site-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img alt="<?php the_title() ?>" src="<?php echo esc_url(get_theme_mod('seller_logo')); ?>"></a>
                </div>
            <?php endif; ?>
            <div id="text-title-desc">
                <h1 class="site-title title-font"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
            </div>
        </div>

        <?php get_template_part('modules/navigation/primary','menu');?>
    </div><!--.container-->

</header><!-- #masthead -->
