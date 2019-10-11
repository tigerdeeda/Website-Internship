<?php
/**
 * Created by PhpStorm.
 * User: Gourav
 * Date: 1/10/2018
 * Time: 9:27 PM
 */


function seller_customize_register_layouts( $wp_customize ) {

//seller layout
$wp_customize->add_panel( 'seller_design_panel', array(
    'priority'       => 3,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
    'title'          => __('Design & Layout','seller'),
) );

//Blog Layout
$wp_customize->add_section(
    'seller_design_options',
    array(
        'title'     => __('Blog Layout','seller'),
        'priority'  => 0,
        'panel'     => 'seller_design_panel'
    )
);

$wp_customize->add_setting(
    'seller_blog_layout',
    array( 'sanitize_callback' => 'seller_sanitize_blog_layout' )
);

function seller_sanitize_blog_layout( $input ) {
    if ( in_array($input, array('grid','grid2','grid3') ) )
        return $input;
    else
        return '';
}

$wp_customize->add_control(
    'seller_blog_layout',array(
        'label' => __('Select Layout','seller'),
        'settings' => 'seller_blog_layout',
        'section'  => 'seller_design_options',
        'type' => 'select',
        'choices' => array(
            'grid' => __('Basic Blog Layout','seller'),
            'grid2' => __('Grid - 2 Column', 'seller'),
            'grid3' => __('Grid - 3 Column', 'seller')
        )
    )
);

//Sidebar Layout
$wp_customize->add_section(
    'seller_sidebar_options',
    array(
        'title'     => __('Sidebar Layout','seller'),
        'priority'  => 0,
        'panel'     => 'seller_design_panel'
    )
);

$wp_customize->add_setting(
    'seller_disable_sidebar',
    array( 'sanitize_callback' => 'seller_sanitize_checkbox', 'default'  => false )
);

$wp_customize->add_control(
    'seller_disable_sidebar', array(
        'settings' => 'seller_disable_sidebar',
        'label'    => __( 'Disable Sidebar Everywhere.','seller' ),
        'section'  => 'seller_sidebar_options',
        'type'     => 'checkbox',
    )
);

$wp_customize->add_setting(
    'seller_disable_sidebar_home',
    array( 'sanitize_callback' => 'seller_sanitize_checkbox', 'default'  => false )
);

$wp_customize->add_control(
    'seller_disable_sidebar_home', array(
        'settings' => 'seller_disable_sidebar_home',
        'label'    => __( 'Disable Sidebar on Home/Blog.','seller' ),
        'section'  => 'seller_sidebar_options',
        'type'     => 'checkbox',
        'active_callback' => 'seller_show_sidebar_options',
    )
);

$wp_customize->add_setting(
    'seller_disable_sidebar_front',
    array( 'sanitize_callback' => 'seller_sanitize_checkbox', 'default'  => true )
);

$wp_customize->add_control(
    'seller_disable_sidebar_front', array(
        'settings' => 'seller_disable_sidebar_front',
        'label'    => __( 'Disable Sidebar on Front Page.','seller' ),
        'section'  => 'seller_sidebar_options',
        'type'     => 'checkbox',
        'active_callback' => 'seller_show_sidebar_options',
    )
);

/* Active Callback Function */
function seller_show_sidebar_options($control) {

    $option = $control->manager->get_setting('seller_disable_sidebar');
    return $option->value() == false ;

}


//Custom Footer Text
$wp_customize-> add_section(
    'seller_custom_footer',
    array(
        'title'			=> __('Custom Footer Text','seller'),
        'description'	=> __('Enter your Own Copyright Text.','seller'),
        'priority'      =>  1,
        'panel'			=> 'seller_design_panel'
    )
);

$wp_customize->add_setting(
    'seller_footer_text',
    array(
        'default'		=> '',
        'sanitize_callback'	=> 'sanitize_text_field'
    )
);

$wp_customize->add_control(
    'seller_footer_text',
    array(
        'section' => 'seller_custom_footer',
        'settings' => 'seller_footer_text',
        'type' => 'text'
    )
);

    class Seller_Custom_CSS_Control extends WP_Customize_Control {
        public $type = 'textarea';

        public function render_content() {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <textarea rows="8" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            </label>
            <?php
        }
    }

    $wp_customize-> add_section(
        'seller_custom_codes',
        array(
            'title'			=> __('Custom CSS','seller'),
            'description'	=> __('Enter your Custom CSS to Modify design.','seller'),
            'priority'		=> 41,
            'panel'			=> 'seller_design_panel'
        )
    );

    $wp_customize->add_setting(
        'seller_custom_css',
        array(
            'default'		=> '',
            'capability'           => 'edit_theme_options',
            'sanitize_callback'    => 'wp_filter_nohtml_kses',
            'sanitize_js_callback' => 'wp_filter_nohtml_kses'
        )
    );

    $wp_customize->add_control(
        new Seller_Custom_CSS_Control(
            $wp_customize,
            'seller_custom_css',
            array(
                'section' => 'seller_custom_codes',


            )
        )
    );

    function seller_sanitize_text( $input ) {
        return wp_kses_post( force_balance_tags( $input ) );
    }

    $wp_customize-> add_section(
        'seller_custom_footer',
        array(
            'title'			=> __('Custom Footer Text','seller'),
            'description'	=> __('Enter your Own Copyright Text.','seller'),
            'priority'		=> 42,
        )
    );

    $wp_customize->add_setting(
        'seller_footer_text',
        array(
            'default'		=> '',
            'sanitize_callback'	=> 'sanitize_text_field'
        )
    );

    $wp_customize->add_control(
        'seller_footer_text',
        array(
            'section' => 'seller_custom_footer',
            'settings' => 'seller_footer_text',
            'type' => 'text'
        )
    );
//page title header
    $wp_customize->add_section(
        'seller_page_title_sec',
        array(
            'title'     => __('Page Title Design','seller'),
            'priority'  => 0,
            'panel'     => 'seller_design_panel'
        )
    );

    $wp_customize->add_setting(
        'seller_page_title_set',
        array( 'sanitize_callback' => 'seller_sanitize_page_title_layout' ,'default' => 'default')
    );

    function seller_sanitize_page_title_layout( $input ) {
        if ( in_array($input, array('default','style1') ) )
            return $input;
        else
            return '';
    }

    $wp_customize->add_control(
        'seller_page_title_set',array(
            'label' => __('Select Design','seller'),
            'settings' => 'seller_page_title_set',
            'section'  => 'seller_page_title_sec',
            'type' => 'select',
            'choices' => array(
                'default' => __('Default','seller'),
                'style1' => __('style 1', 'seller'),
            )
        )
    );


}
add_action( 'customize_register', 'seller_customize_register_layouts' );