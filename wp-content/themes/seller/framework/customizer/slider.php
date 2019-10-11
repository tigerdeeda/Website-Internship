<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 9:50 PM
 */


function seller_customize_register_slider( $wp_customize ) {
// SLIDER PANEL
$wp_customize->add_panel( 'seller_slider_panel', array(
    'priority'       => 4,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => 'Main Slider',
) );

$wp_customize->add_section(
    'seller_sec_slider_options',
    array(
        'title'     => __('Enable/Disable','seller'),
        'priority'  => 0,
        'panel'     => 'seller_slider_panel'
    )
);


$wp_customize->add_setting(
    'seller_main_slider_enable',
    array( 'sanitize_callback' => 'seller_sanitize_checkbox' )
);

$wp_customize->add_control(
    'seller_main_slider_enable', array(
        'settings' => 'seller_main_slider_enable',
        'label'    => __( 'Enable Slider.', 'seller' ),
        'section'  => 'seller_sec_slider_options',
        'type'     => 'checkbox',
    )
);

$wp_customize->add_setting(
    'seller_main_slider_count',
    array(
        'default' => '0',
        'sanitize_callback' => 'seller_sanitize_positive_number'
    )
);

// Select How Many Slides the User wants, and Reload the Page.
$wp_customize->add_control(
    'seller_main_slider_count', array(
        'settings' => 'seller_main_slider_count',
        'label'    => __( 'No. of Slides(Min:0, Max: 5)' ,'seller'),
        'section'  => 'seller_sec_slider_options',
        'type'     => 'number',
        'description' => __('Save the Settings, and Reload this page to Configure the Slides.','seller'),

    )
);



if ( get_theme_mod('seller_main_slider_count') > 0 ) :
    $slides = get_theme_mod('seller_main_slider_count');

    for ( $i = 1 ; $i <= $slides ; $i++ ) :

        //Create the settings Once, and Loop through it.

        $wp_customize->add_setting(
            'seller_slide_img'.$i,
            array( 'sanitize_callback' => 'esc_url_raw' )
        );

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize,
                'seller_slide_img'.$i,
                array(
                    'label' => '',
                    'section' => 'seller_slide_sec'.$i,
                    'settings' => 'seller_slide_img'.$i,
                )
            )
        );


        $wp_customize->add_section(
            'seller_slide_sec'.$i,
            array(
                'title'     => 'Slide '.$i,
                'priority'  => $i,
                'panel'     => 'seller_slider_panel'
            )
        );

        $wp_customize->add_setting(
            'seller_slide_title'.$i,
            array( 'sanitize_callback' => 'sanitize_text_field' )
        );

        $wp_customize->add_control(
            'seller_slide_title'.$i, array(
                'settings' => 'seller_slide_title'.$i,
                'label'    => __( 'Slide Title','seller' ),
                'section'  => 'seller_slide_sec'.$i,
                'type'     => 'text',
            )
        );

        $wp_customize->add_setting(
            'seller_slide_desc'.$i,
            array( 'sanitize_callback' => 'sanitize_text_field' )
        );

        $wp_customize->add_control(
            'seller_slide_desc'.$i, array(
                'settings' => 'seller_slide_desc'.$i,
                'label'    => __( 'Slide Description','seller' ),
                'section'  => 'seller_slide_sec'.$i,
                'type'     => 'text',
            )
        );


        $wp_customize->add_setting(
            'seller_slide_url'.$i,
            array( 'sanitize_callback' => 'esc_url_raw' )
        );

        $wp_customize->add_control(
            'seller_slide_url'.$i, array(
                'settings' => 'seller_slide_url'.$i,
                'label'    => __( 'Target URL','seller' ),
                'section'  => 'seller_slide_sec'.$i,
                'type'     => 'url',
            )
        );

    endfor;


endif;
}
add_action( 'customize_register', 'seller_customize_register_slider' );