<div id="top-bar">
    <div class="container">
        <div class="col-md-6">
            <?php if (get_theme_mod('seller_email')) : ?>
                <span class="top-left">
		<i class="fa fa-envelope"></i>
        <a href="mailto:<?php echo esc_html( get_theme_mod('seller_email') )?>">
            <?php echo esc_html( get_theme_mod('seller_email') ); ?>
        </a>

	</span>
            <?php endif; ?>
            <?php if (get_theme_mod('seller_phone')) :?>
                <span class="top-left">
		<i class="fa fa-phone"></i>
        <a href="tel:<?php echo esc_html( get_theme_mod('seller_phone') )?>">
            <?php echo esc_html( get_theme_mod('seller_phone') ); ?>
        </a>
	</span>
            <?php endif; ?>
        </div>
        <div id="social-icons" class="col-md-6">
            <?php get_template_part('modules/social/social', 'fa'); ?>
            <div id="search-icon">
                <a id="searchicon">
                    <span class="fa fa-search"></span>
                </a>
            </div>
        </div>
    </div><!--.container-->
</div><!--#top-bar-->