<?php
/**
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Seller
 */
?>
<?php get_template_part('modules/header/head'); ?>

<body <?php body_class(); ?>>
<?php global $option_setting; ?>
<div id="page" class="hfeed site">

            <?php
                get_template_part('modules/header/jumbosearch');
                get_template_part('modules/header/top','bar');
                get_template_part('modules/header/masthead');
                get_template_part('framework/featured-components/slider');
                get_template_part('framework/featured-components/showcase');
	        ?>

	
	<div id="content" class="site-content container">