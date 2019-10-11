<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 9:30 PM
 */
function seller_customize_register_social( $wp_customize ) {
// Social Icons
$wp_customize->add_section('seller_social_section', array(
    'title' => __('Social Icons','seller'),
    'priority' => 44 ,
    'panel'      => 'seller_header_panel'
));

$social_networks = array( //Redefinied in Sanitization Function.
    'none' => __('-','seller'),
    'facebook' => __('Facebook','seller'),
    'twitter' => __('Twitter','seller'),
    'google-plus' => __('Google Plus','seller'),
    'instagram' => __('Instagram','seller'),
    'rss' => __('RSS Feeds','seller'),
    'flickr' => __('Flickr','seller'),
    'vine' => __('Vine','seller'),
    'vimeo-square' => __('Vimeo','seller'),
    'youtube' => __('Youtube','seller'),
);

$social_count = count($social_networks);

for ($x = 1 ; $x <= ($social_count - 3) ; $x++) :

    $wp_customize->add_setting(
        'seller_social_'.$x, array(
        'sanitize_callback' => 'seller_sanitize_social',
        'default' => 'none'
    ));

    $wp_customize->add_control( 'seller_social_'.$x, array(
        'settings' => 'seller_social_'.$x,
        'label' => __('Icon ','seller').$x,
        'section' => 'seller_social_section',
        'type' => 'select',
        'choices' => $social_networks,
    ));

    $wp_customize->add_setting(
        'seller_social_url'.$x, array(
        'sanitize_callback' => 'esc_url_raw'
    ));

    $wp_customize->add_control( 'seller_social_url'.$x, array(
        'settings' => 'seller_social_url'.$x,
        'description' => __('Icon ','seller').$x.__(' Url','seller'),
        'section' => 'seller_social_section',
        'type' => 'url',
        'choices' => $social_networks,
    ));

endfor;

function seller_sanitize_social( $input ) {
    $social_networks = array(
        'none' ,
        'facebook',
        'twitter',
        'google-plus',
        'instagram',
        'rss',
        'flickr',
        'vimeo-square',
        'youtube',
        'vine'
    );
    if ( in_array($input, $social_networks) )
        return $input;
    else
        return '';
}


}
add_action( 'customize_register', 'seller_customize_register_social' );