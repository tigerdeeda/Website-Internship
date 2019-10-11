<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 9:32 PM
 */
function seller_customize_register_misc( $wp_customize ) {

    if (class_exists('WP_Customize_Control')) {
        class Seller_WP_Customize_Upgrade_Control extends WP_Customize_Control {
            /**
             * Render the control's content.
             */
            public function render_content() {
                printf(
                    '<label class="customize-control-upgrade"><span class="customize-control-title">%s</span> %s</label>',
                    $this->label,
                    $this->description
                );
            }
        }
    }


    //Upgrade
    $wp_customize->add_section(
        'seller_sec_upgrade',
        array(
            'title'     => __('Discover Seller Pro','seller'),
            'priority'  => 1,
        )
    );

    $wp_customize->add_setting(
        'seller_upgrade',
        array( 'sanitize_callback' => 'esc_textarea' )
    );

    $wp_customize->add_control(
        new Seller_WP_Customize_Upgrade_Control(
            $wp_customize,
            'seller_upgrade',
            array(
                'label' => __('More of Everything','seller'),
                'description' => __('Seller Pro has more of Everything. More New Features, More Options, Unlimited Colors, More Fonts, More Layouts, Configurable Slider, Inbuilt Advertising Options, More Widgets, and a lot more options and comes with Dedicated Support. To Know More about the Pro Version, click here: <a href="http://inkhive.com/product/seller-pro/">Upgrade to Pro</a>.','seller'),
                'section' => 'seller_sec_upgrade',
                'settings' => 'seller_upgrade',
            )
        )
    );
}
add_action( 'customize_register', 'seller_customize_register_misc' );