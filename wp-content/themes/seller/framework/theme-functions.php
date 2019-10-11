<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 12:41 PM
 */
/*
** Function to Get Theme Layout 
*/
function seller_get_blog_layout(){
    $ldir = 'framework/layouts/content';
    if (get_theme_mod('seller_blog_layout') ) :
        get_template_part( $ldir , get_theme_mod('seller_blog_layout') );
    else :
        get_template_part( $ldir ,'grid');
    endif;
}
add_action('seller_blog_layout', 'seller_get_blog_layout');



/*
**	Determining Sidebar and Primary Width
*/
function seller_primary_class() {
    $sw = esc_html(get_theme_mod('seller_sidebar_width',4));
    $class = "col-md-".(12-$sw);

    if ( !seller_load_sidebar() )
        $class = "col-md-12";

    echo $class;
}
add_action('seller_primary-width', 'seller_primary_class');

function seller_secondary_class() {
    $sw = esc_html(get_theme_mod('seller_sidebar_width',4));
    $class = "col-md-".$sw;

    echo $class;
}
add_action('seller_secondary-width', 'seller_secondary_class');