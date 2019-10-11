<?php
//Fonts
function seller_customize_register_fonts( $wp_customize ) {
    $wp_customize->add_section(
        'seller_typo_options',
        array(
            'title'     => __('Google Web Fonts','seller'),
            'priority'  => 41,
            'panel'     => 'seller_design_panel'
        )
    );

    $font_array = array('Helvetica','Arial','sans-sarif','Lato','Khula','Open Sans','Droid Sans','Droid Serif','Roboto Condensed','Bree Serif','Oswald','Slabo','Lora');
    $fonts = array_combine($font_array, $font_array);

    $wp_customize->add_setting(
        'seller_title_font',
        array(
            'default'=> 'Helvetica',
            'sanitize_callback' => 'seller_sanitize_gfont'
        )
    );

    function seller_sanitize_gfont( $input ) {
        if ( in_array($input, array('Helvetica','Arial','sans-sarif','Lato','Khula','Open Sans','Droid Sans','Droid Serif','Roboto Condensed','Bree Serif','Oswald','Slabo','Lora') ) )
            return $input;
        else
            return '';
    }

    $wp_customize->add_control(
        'seller_title_font',array(
            'label' => __('Title','seller'),
            'settings' => 'seller_title_font',
            'section'  => 'seller_typo_options',
            'type' => 'select',
            'choices' => $fonts,
        )
    );

    $wp_customize->add_setting(
        'seller_body_font',
        array(	'default'=> 'Droid Sans',
            'sanitize_callback' => 'seller_sanitize_gfont' )
    );

    $wp_customize->add_control(
        'seller_body_font',array(
            'label' => __('Body','seller'),
            'settings' => 'seller_body_font',
            'section'  => 'seller_typo_options',
            'type' => 'select',
            'choices' => $fonts
        )
    );
    //site description Font size end
}
add_action( 'customize_register', 'seller_customize_register_fonts' );