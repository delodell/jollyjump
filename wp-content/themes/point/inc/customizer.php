<?php
/**
 * point Theme Customizer.
 *
 * @package point
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

$cats = strtr(wp_list_categories("&style=none&echo=0"),array('<br />'=>', '));

function point_customize_register( $wp_customize ) {

    /**
     * Multiple select customize control class.
     */
    class Jayj_Customize_Control_Multiple_Select extends WP_Customize_Control {

        /**
         * The type of customize control being rendered.
         */
        public $type = 'multiple-select';

        /**
         * Displays the multiple select on the customize screen.
         */
        public function render_content() {

        if ( empty( $this->choices ) )
            return;
        ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <select <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
                    <?php
                        foreach ( $this->choices as $value => $label ) {
                            $selected = is_array( $this->value() ) ? in_array( $value, $this->value() ) ? selected( 1, 1, false ) : '' : '';
                            echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';
                        }
                    ?>
                </select>
            </label>
        <?php }
    }

	/*---------------------
	* Theme Options
	----------------------*/
    $wp_customize->add_panel( 'panel_id', array(
        'priority'       => 121,
        'capability'     => 'edit_theme_options',
        'title'          => __('Theme Options', 'point'),
        'description'    => __('Several settings of point', 'point'),
    ) );

    /***************************************************/
    /*****                 Styling                 ****/
    /**************************************************/
    $wp_customize->add_section( 'styling_settings', array(
        'title'          => __('Styling Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );

    //Layout
    $wp_customize->add_setting('point_layout', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_setting('point_layout', array(
        'default'        => 'cslayout',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_layout', array(
        'settings' => 'point_layout',
        'label'    => __('Sidebar Position', 'point'),
        'section'  => 'styling_settings',
        'type'     => 'radio',
        'choices'    => array(
            'cslayout' => __('Right Sidebar','point'),
            'sclayout' => __('Left Sidebar','point'),
        ),
    ));

    //Color Scheme
    $wp_customize->add_setting( 'point_color_scheme', array(
        'default'           => '#38B7EE',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'point_color_scheme', array(
        'label'         => __('Pick Color','point'),
        'section'       => 'styling_settings',
        'settings' => 'point_color_scheme',
    )) );

    //Full posts
    $wp_customize->add_setting('point_full_posts', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_setting('point_full_posts', array(
        'default'        => '0',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_full_posts', array(
        'settings' => 'point_full_posts',
        'label'    => __('Posts on blog pages', 'point'),
        'section'  => 'styling_settings',
        'type'     => 'radio',
        'choices'    => array(
            '0' => __('Excerpts','point'),
            '1' => __('Full Posts','point'),
        ),
    ));

    //RTL Support
    $wp_customize->add_setting('point_rtl', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw',
        'transport' => 'refresh'
    ));
    $wp_customize->add_setting('point_rtl', array(
        'default'        => '0',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_rtl', array(
        'label'      => __('Right To Left Language Support', 'point'),
        'section'    => 'styling_settings',
        'settings' => 'point_rtl',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));

    // Custom CSS
    $wp_customize->add_setting('custom_css', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw'
    )); 
    $wp_customize->add_control('custom_css', array(
        'label'      => __('Custom CSS', 'point'),
        'section'    => 'styling_settings',
        'settings'   => 'custom_css',
        'type'           => 'textarea',
    ));

    /***************************************************/
    /*****                Trending                 ****/
    /**************************************************/
    $wp_customize->add_section( 'trending_settings', array(
        'title'          => __('Trending Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );

    $wp_customize->add_setting('point_trending_section', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'transport' => 'refresh',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_setting('point_trending_section', array(
        'default'        => '0',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_trending_section', array(
        'label'      => __('Trending Section', 'point'),
        'section'    => 'trending_settings',
        'settings' => 'point_trending_section',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));

    //trending category
    $wp_customize->add_setting('point_trending_cat', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw'
    ));

    // =====================
    //  = Category Dropdown =
    //  =====================
    $categories = get_categories();
    $cats = array();
    $i = 0;
    foreach($categories as $category){
        if($i==0){
            $default = $category->slug;
            $i++;
        }
        $cats[$category->slug] = $category->name;
    }
 
    $wp_customize->add_setting('point_trending_cat', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control( new Jayj_Customize_Control_Multiple_Select( $wp_customize, 'point_trending_cat', array(
        'settings' => 'point_trending_cat',
        'label'   => __('Select Category:', 'point'),
        'section'  => 'trending_settings',
        'type'    => 'multiple-select',
        'choices' => $cats,
    )));

    /***************************************************/
    /*****                  Header                 ****/
    /**************************************************/
    // Button and Text
    $wp_customize->add_section( 'point_header_button', array(
        'title'          => __('Header Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );

    // Header Code
    $wp_customize->add_setting('point_header_code', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw'
    )); 
    $wp_customize->add_control('point_header_code', array(
        'label'      => __('Header Code', 'point'),
        'description'=> __('Use this option to insert any verification code in &lt;head&gt; Tag.', 'point'),
        'section'    => 'point_header_button',
        'settings'   => 'point_header_code',
        'type'       => 'textarea',
    ));

    // Header Ad
    $wp_customize->add_setting('point_header_ad_code', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw'
    )); 
    $wp_customize->add_control('header_ad_code', array(
        'label'      => __('Header Ad Code (728x90)', 'point'),
        'section'    => 'point_header_button',
        'settings'   => 'point_header_ad_code',
        'type'       => 'textarea',
    ));

    $wp_customize->add_setting( 'point_button_bg_color', array(
        'default'           => '#38B7EE',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'point_button_bg_color', array(
        'label'         => __('Pick Color for Button','point'),
        'section'       => 'point_header_button',
        'settings' => 'point_button_bg_color',
    )) );

    $wp_customize->add_setting( 'point_button_text', array(
        'default' => 'Download!',
        'sanitize_callback' == 'esc_url_raw'
    ) );
    $wp_customize->add_control( 'point_button_text', array(
        'label'   => __('Button Text','point'),
        'section' => 'point_header_button',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_setting( 'point_button_url', array(
        'default' => 'https://mythemeshop.com/themes/point/',
        'sanitize_callback' == 'esc_url_raw'
    ) );
    $wp_customize->add_control( 'point_button_url', array(
        'label'   => __('Button URL','point'),
        'section' => 'point_header_button',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

    $wp_customize->add_setting( 'point_bottom_text', array(
        'default' => ' Download Point responsive WP Theme for FREE!',
        'sanitize_callback' == 'esc_url_raw'
    ) );
    $wp_customize->add_control( 'point_bottom_text', array(
        'label'   => __('Bottom Text','point'),
        'section' => 'point_header_button',
        'type'    => 'text',
        'transport'   => 'refresh',
    ) );

    /***************************************************/
    /*****                 Feature                 ****/
    /**************************************************/
    $wp_customize->add_section( 'feature_settings', array(
        'title'          => __('Feature Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );

    $wp_customize->add_setting('point_feature_setting', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw',
        'transport' => 'refresh'
    ));
    $wp_customize->add_setting('point_feature_setting', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw',
    ));
    $wp_customize->add_control('point_feature_setting', array(
        'label'      => __('Featured Section', 'point'),
        'section'    => 'feature_settings',
        'settings' => 'point_feature_setting',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));

    //feature category
    $wp_customize->add_setting('point_feature_cat', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw'
    ));

    // =====================
    //  = Category Dropdown =
    //  =====================
    $categories = get_categories();
    $cats = array();
    $i = 0;
    foreach($categories as $category){
        if($i==0){
            $default = $category->slug;
            $i++;
        }
        $cats[$category->slug] = $category->name;
    }
 
    $wp_customize->add_setting('point_feature_cat', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw',
    ));
    $wp_customize->add_control( new Jayj_Customize_Control_Multiple_Select( $wp_customize, 'point_feature_cat', array(
        'settings' => 'point_feature_cat',
        'label'   => __('Select Category:', 'point'),
        'section'  => 'feature_settings',
        'type'    => 'multiple-select',
        'choices' => $cats,
    )));

    /***************************************************/
    /*****               pagination                ****/
    /**************************************************/
    $wp_customize->add_section( 'point_pagination_settings', array(
        'title'          => __('Pagination Type','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );

    $wp_customize->add_setting( 'point_pagination_type', array(
        'default' => '1',
        'capability' => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw'
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'point_pagination_type',
            array(
                'label'          => __('Pagination Type', 'point'),
                'section'        => 'point_pagination_settings',
                'settings'       => 'point_pagination_type',
                'type'           => 'radio',
                'choices'    => array(
                    '0'   => __('Next/Previous', 'point'),
                    '1'  => __('Numbered', 'point'),
                    ),
                'transport'   => 'refresh',
            )
        )
    );

    /***************************************************/
    /*****            Footer Carousel              ****/
    /**************************************************/
    $wp_customize->add_section( 'footer_carousel_settings', array(
        'title'          => __('Footer Featured Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ));

    $wp_customize->add_setting('point_carousel_section', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw',
        'transport' => 'refresh'
    ));
    $wp_customize->add_setting('point_carousel_section', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw',
    ));
    $wp_customize->add_control('point_carousel_section', array(
        'label'      => __('Footer Featured Section', 'point'),
        'section'    => 'footer_carousel_settings',
        'settings' => 'point_carousel_section',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));

    //carousel category
    $wp_customize->add_setting('point_carousel_cat', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw'
    ));

    // =====================
    //  = Category Dropdown =
    //  =====================
    $categories = get_categories();
    $cats = array();
    $i = 0;
    foreach($categories as $category){
        if($i==0){
            $default = $category->slug;
            $i++;
        }
        $cats[$category->slug] = $category->name;
    }
 
    $wp_customize->add_setting('point_carousel_cat', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control( new Jayj_Customize_Control_Multiple_Select( $wp_customize, 'point_carousel_cat', array(
        'settings' => 'point_carousel_cat',
        'label'   => __('Select Category:', 'point'),
        'section'  => 'footer_carousel_settings',
        'type'    => 'multiple-select',
        'choices' => $cats,
    )));

    //  =============================
    //  = Footer Logo               =
    //  =============================
    $wp_customize->add_section( 'footer_settings', array(
        'title'          => __('Footer Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );
    $wp_customize->add_setting('point_footer_logo', array(
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw' 
    ));
    $wp_customize->add_setting('point_footer_logo', array(
        'default'        => get_template_directory() . '/images/footerlogo.png',
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'point_footer_logo', array(
        'label'    => __('Footer Logo', 'point'),
        'section'  => 'footer_settings',
        'settings' => 'point_footer_logo',
    )));

    //Copyright Text
    $wp_customize->add_setting('copyright_text', array(
        'default'        => 'Theme by <a href="http://mythemeshop.com/">MyThemeShop</a>.',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw' 
    )); 
    $wp_customize->add_control('copyright_text', array(
        'label'      => __('Copyrights Text', 'point'),
        'section'    => 'footer_settings',
        'settings'   => 'copyright_text',
        'type'           => 'textarea',
    ));

    //  =============================
    //  = Text Input                =
    //  =============================
    $wp_customize->add_section( 'single_settings', array(
        'title'          => __('Single Post Settings','point'),
        'priority'       => 122,
        'capability'     => 'edit_theme_options',
        'panel'  => 'panel_id',
    ) );
    //Below Post Title
    $wp_customize->add_setting('point_single_adcode', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw' 
    )); 
    $wp_customize->add_control('point_single_adcode', array(
        'label'      => __('Ad Code - Below Post Title', 'point'),
        'section'    => 'single_settings',
        'settings'   => 'point_single_adcode',
        'type'           => 'textarea',
    ));

    $wp_customize->add_setting('point_single_adcode_days', array(
        'default'        => '0',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw' 
    )); 
    $wp_customize->add_control('point_single_adcode_days', array(
        'label'      => __('Show After X Days', 'point'),
        'section'    => 'single_settings',
        'settings'   => 'point_single_adcode_days',
    ));

    //Below Post Content
    $wp_customize->add_setting('point_single_adcode_below', array(
        'default'        => '',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw'
    )); 
    $wp_customize->add_control('point_single_adcode_below', array(
        'label'      => __('Ad Code - Below Post Content', 'point'),
        'section'    => 'single_settings',
        'settings'   => 'point_single_adcode_below',
        'type'           => 'textarea',
    ));

    $wp_customize->add_setting('point_single_adcode_days_below', array(
        'default'        => '0',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' == 'esc_url_raw'
    )); 
    $wp_customize->add_control('point_single_adcode_days_below', array(
        'label'      => __('Show After X Days', 'point'),
        'section'    => 'single_settings',
        'settings'   => 'point_single_adcode_days_below',
    ));

    //Tags
    $wp_customize->add_setting('point_single_tags_section', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw',
        'transport' => 'refresh'
    ));
    $wp_customize->add_setting('point_single_tags_section', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_single_tags_section', array(
        'label'      => __('Tags Section', 'point'),
        'section'    => 'single_settings',
        'settings' => 'point_single_tags_section',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));

    //Related Posts
    $wp_customize->add_setting('point_relatedposts_section', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw',
        'transport' => 'refresh'
    ));
    $wp_customize->add_setting('point_relatedposts_section', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_relatedposts_section', array(
        'label'      => __('Related Posts Section', 'point'),
        'section'    => 'single_settings',
        'settings' => 'point_relatedposts_section',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));

    //Author Box
    $wp_customize->add_setting('point_authorbox_section', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' == 'esc_url_raw',
        'transport' => 'refresh'
    ));
    $wp_customize->add_setting('point_authorbox_section', array(
        'default'        => $default,
        'sanitize_callback' == 'esc_url_raw'
    ));
    $wp_customize->add_control('point_authorbox_section', array(
        'label'      => __('Author box Section', 'point'),
        'section'    => 'single_settings',
        'settings' => 'point_authorbox_section',
        'type'       => 'radio',
        'choices'    => array(
            '0' => __('OFF', 'point'),
            '1' => __('ON', 'point'),
        ),
    ));


	$wp_customize->get_setting( 'blogname' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport  = 'refresh';

    $wp_customize->get_setting( 'point_layout' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_color_scheme' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_full_posts' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_rtl' )->transport  = 'refresh';

    $wp_customize->get_setting( 'point_header_code' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_header_ad_code' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_button_bg_color' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_button_text' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_button_url' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_bottom_text' )->transport  = 'refresh';

    $wp_customize->get_setting( 'point_trending_section' )->transport  = 'refresh';
	$wp_customize->get_setting( 'point_trending_cat' )->transport  = 'refresh';

	$wp_customize->get_setting( 'point_feature_setting' )->transport  = 'refresh';
	$wp_customize->get_setting( 'point_feature_cat' )->transport  = 'refresh';

    $wp_customize->get_setting( 'point_pagination_type' )->transport  = 'refresh';

    $wp_customize->get_setting( 'point_carousel_section' )->transport  = 'refresh';
    $wp_customize->get_setting( 'point_carousel_cat' )->transport    = 'refresh';

    $wp_customize->get_setting( 'point_footer_logo' )->transport    = 'refresh';
    $wp_customize->get_setting( 'copyright_text' )->transport    = 'refresh';

    $wp_customize->get_setting( 'point_single_adcode' )->transport    = 'refresh';
    $wp_customize->get_setting( 'point_single_adcode_days' )->transport    = 'refresh';
    $wp_customize->get_setting( 'point_single_adcode_below' )->transport    = 'refresh';
    $wp_customize->get_setting( 'point_single_adcode_days_below' )->transport    = 'refresh';

    $wp_customize->get_setting( 'point_single_tags_section' )->transport    = 'refresh';
    
    $wp_customize->get_setting( 'point_relatedposts_section' )->transport    = 'refresh';

    $wp_customize->get_setting( 'point_authorbox_section' )->transport    = 'refresh';

}
add_action( 'customize_register', 'point_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function point_customize_preview_js() {
	wp_enqueue_script( 'point_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'point_customize_preview_js' );
