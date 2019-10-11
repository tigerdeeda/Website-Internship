<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 9:53 PM
 */
function seller_customize_register_showcase( $wp_customize ) {

//Showcase
$wp_customize->add_panel( 'seller_showcase_panel', array(
    'priority'       => 35,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __('Showcase','seller'),
) );

$wp_customize->add_section(
    'seller_sec_showcase_options',
    array(
        'title'     => __('Enable/Disable','seller'),
        'priority'  => 0,
        'panel'     => 'seller_showcase_panel'
    )
);


$wp_customize->add_setting(
    'seller_main_showcase_enable',
    array( 'sanitize_callback' => 'seller_sanitize_checkbox' )
);

$wp_customize->add_control(
    'seller_main_showcase_enable', array(
        'settings' => 'seller_main_showcase_enable',
        'label'    => __( 'Enable Showcase.', 'seller' ),
        'section'  => 'seller_sec_showcase_options',
        'type'     => 'checkbox',
    )
);


$showcases = 3;

for ( $i = 1 ; $i <= $showcases ; $i++ ) :

    //Create the settings Once, and Loop through it.

    $wp_customize->add_setting(
        'seller_showcase_img'.$i,
        array( 'sanitize_callback' => 'esc_url_raw' )
    );

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'seller_showcase_img'.$i,
            array(
                'label' => '',
                'section' => 'seller_showcase_sec'.$i,
                'settings' => 'seller_showcase_img'.$i,
            )
        )
    );


    $wp_customize->add_section(
        'seller_showcase_sec'.$i,
        array(
            'title'     => 'Showcase '.$i,
            'priority'  => $i,
            'panel'     => 'seller_showcase_panel'
        )
    );

    $wp_customize->add_setting(
        'seller_showcase_title'.$i,
        array( 'sanitize_callback' => 'sanitize_text_field' )
    );

    $wp_customize->add_control(
        'seller_showcase_title'.$i, array(
            'settings' => 'seller_showcase_title'.$i,
            'label'    => __( 'Showcase Title','seller' ),
            'section'  => 'seller_showcase_sec'.$i,
            'type'     => 'text',
        )
    );

    $wp_customize->add_setting(
        'seller_showcase_desc'.$i,
        array( 'sanitize_callback' => 'sanitize_text_field' )
    );

    $wp_customize->add_control(
        'seller_showcase_desc'.$i, array(
            'settings' => 'seller_showcase_desc'.$i,
            'label'    => __( 'Showcase Description','seller' ),
            'section'  => 'seller_showcase_sec'.$i,
            'type'     => 'text',
        )
    );


    $wp_customize->add_setting(
        'seller_showcase_url'.$i,
        array( 'sanitize_callback' => 'esc_url_raw' )
    );

    $wp_customize->add_control(
        'seller_showcase_url'.$i, array(
            'settings' => 'seller_showcase_url'.$i,
            'label'    => __( 'Target URL','seller' ),
            'section'  => 'seller_showcase_sec'.$i,
            'type'     => 'url',
        )
    );

endfor;

}
add_action( 'customize_register', 'seller_customize_register_showcase' );