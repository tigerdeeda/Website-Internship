<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 3:48 PM
 */


function seller_custom_css_mods() {

    $custom_css = "";
    if ( get_theme_mod('seller_title_font') ) :
        //var_dump(get_theme_mod('seller_title_font'));
        $custom_css .= ".site-title,.header-title.title-font,h1,h2,.section-title { font-family: ".esc_html(get_theme_mod('seller_title_font','Helvetica'))."; }";
    endif;

    if ( get_theme_mod('seller_body_font') ) :
        $custom_css .= "body { font-family: ".esc_html(get_theme_mod('seller_body_font','Droid Sans'))."; }";
    endif;
    //Sidebar control
        if( get_theme_mod('seller_disable_sidebar')):
            $custom_css .= "#content #secondary { display:none; }
                            #content #primary{width:100%}";
        endif;

        if(is_home()):
            if( get_theme_mod('seller_disable_sidebar_home',true)):
                $custom_css .= ".home #secondary { display:none; }
                            .home #primary{width:100%}";
            endif;
        endif;

        if(is_front_page() && !is_home()):
            if( get_theme_mod('seller_disable_sidebar_front',true)):
                $custom_css .= ".home #secondary { display:none; }
                                .home #primary-mono{width:100%}";
            endif;
        endif;

        // Page header title
    if( get_theme_mod('seller_page_title_set')=='style1'):
        $custom_css .= ".header-title { background: #A1CAE0;
                        border: solid 3px #fff;
                        box-shadow: 5px 10px 1px #888888;
                        color: #fff;}";
    endif;

    //hide custom logo
    if( get_theme_mod('seller_hide_title_tagline',true)):
        $custom_css .= "#text-title-desc{display:none;}";
    endif;

    wp_add_inline_style( 'seller-theme-structure', wp_strip_all_tags($custom_css) );



}

add_action('wp_enqueue_scripts', 'seller_custom_css_mods');