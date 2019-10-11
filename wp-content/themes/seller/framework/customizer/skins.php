<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 9:29 PM
 */
function seller_customize_register_skins( $wp_customize ) {
    //skins
    //Select the Default Theme Skin
    $wp_customize->add_setting(
        'seller_skin',
        array(
            'default'=> 'default',
            'sanitize_callback' => 'seller_sanitize_skin'
        )
    );

    $skins = array( 'default' => __('Default(Blue)','seller'),
        'affair' =>  __('Affair','seller'),
        'green' => __('Green','seller'),
        'coral' => __('Coral','seller'),
    );

    $wp_customize->add_control(
        'seller_skin',array(
            'settings' => 'seller_skin',
            'section'  => 'colors',
            'label' => __('Choose Skin', 'seller'),
            'description' => __('seller theme has 3 skins Orange, Pink and Slick', 'seller'),
            'type' => 'select',
            'choices' => $skins,
        )
    );

    function seller_sanitize_skin( $input ) {
        if ( in_array($input, array('default','affair','green','coral') ) )
            return $input;
        else
            return '';
    }
}
add_action( 'customize_register', 'seller_customize_register_skins' );