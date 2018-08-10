<?php
/**
 * point functions and definitions.
 */

if ( ! function_exists( 'point_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function point_setup() {
	define( 'MTS_THEME_VERSION', '2.0.3' );
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on point, use a find and replace
	 * to change 'point' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'point', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 220, 162, true );
	add_image_size( 'featured', 220, 162, true ); //Latest posts thumb
	add_image_size( 'carousel', 140, 130, true ); //Bottom featured thumb
	add_image_size( 'bigthumb', 620, 315, true ); //Big thumb for featured area
	add_image_size( 'mediumthumb', 300, 200, true ); //Medium thumb for featured area
	add_image_size( 'smallthumb', 140, 100, true ); //Small thumb for featured area
	add_image_size( 'widgetthumb', 60, 57, true ); //widget

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'point' ),
		'footer' => esc_html__( 'Footer Menu', 'point' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'point_custom_background_args', array(
		'default-color' => 'e7e5e6',
		'default-image' => '',
	) ) );

	/* Display a admin notice */

    add_action('admin_notices', 'point_admin_notice');
    function point_admin_notice() {
        global $current_user ;
        $user_id = $current_user->ID;
        /* Check that the user hasn't already clicked to ignore the message */
        if ( ! get_user_meta($user_id, 'point_ignore_notice') ) {
            echo '<div class="updated notice-info point-notice" style="position:relative;"><p>'; 
            printf(__('Like Point theme? You will <strong>LOVE Point Pro</strong>!','point').'<a href="https://mythemeshop.com/themes/point/?utm_source=Point+Free&utm_medium=Notification+Link&utm_content=Point+Pro+LP&utm_campaign=WordPressOrg" target="_blank">&nbsp;'.__('Click here for all the exciting features.','point').'</a><a href="%1$s" class="dashicons dashicons-dismiss dashicons-dismiss-icon" style="position: absolute; top: 8px; right: 8px; color: #222; opacity: 0.4; text-decoration: none !important;"></a>', '?point_notice_ignore=0');
            echo "</p></div>";
        }
    }

    add_action('admin_init', 'point_notice_ignore');
    function point_notice_ignore() {
        global $current_user;
            $user_id = $current_user->ID;
            /* If user clicks to ignore the notice, add that to their user meta */
            if ( isset($_GET['point_notice_ignore']) && '0' == $_GET['point_notice_ignore'] ) {
                add_user_meta($user_id, 'point_ignore_notice', 'true', true);
        }
    }

    add_action( 'customize_controls_print_footer_scripts', 'point_pro_banner' );
    function point_pro_banner() {
        echo '<a href="https://mythemeshop.com/themes/point/?utm_source=Point+Free&utm_medium=Banner+CPC&utm_content=Point+Pro+LP&utm_campaign=WordPressOrg" id="pro-banner" style="display: none; margin-top: 10px; background: #fff;" target="_blank"><img src="'.get_template_directory_uri().'/images/point-pro.jpg" /></a>';
        echo '<script type="text/javascript">jQuery(document).ready(function($) { $("#pro-banner").appendTo("#customize-info").css("display", "block"); });</script>';
    }

	point_migrate_to_customizer();
}
endif; // point_setup
add_action( 'after_setup_theme', 'point_setup' );

/**
 * Header code
 * @return none
 */

function point_header_code() {
	echo get_theme_mod( 'point_header_code', '' );
}
add_action( 'wp_head', 'point_header_code' );

/**
 * Migrates Theme Options to Customizer at version 2.0
 * @return none
 */
function point_migrate_to_customizer() {
	$old_options = get_option( 'point' );
	if ( ! $old_options ) {
		return;
	}

	$has_migrated = get_option( 'point_has_migrated' );
	if ( $has_migrated ) {
		return;
	}

	$migrate_options = array(
		'mts_logo' => 'header_image',
		'mts_footer_logo' => 'point_footer_logo',
		'mts_header_code' => 'point_header_code',
		'mts_copyrights' => 'copyright_text',
		'mts_trending_articles' => 'point_trending_section',
		'mts_trending_articles_cat' => 'point_trending_cat',
		'mts_featured_slider' => 'point_feature_setting',
		'mts_featured_slider_cat' => 'point_feature_cat',
		'mts_featured_carousel' => 'point_carousel_section',
		'mts_featured_carousel_cat' => 'point_carousel_cat',
		'mts_pagenavigation' => 'point_pagination_type',
		'mts_rtl' => 'point_rtl',
		'mts_color_scheme' => 'point_color_scheme',
		'mts_layout' => 'point_layout',
		'mts_full_posts' => 'point_full_posts',
		'mts_bg_color' => 'background_color',
		'mts_custom_css' => 'custom_css',
		'mts_tags' => 'point_single_tags_section',
		'mts_related_posts' => 'point_relatedposts_section',
		'mts_author_box' => 'point_authorbox_section',
		'mts_header_adcode' => 'point_header_ad_code',
		'mts_posttop_adcode' => 'point_single_adcode',
		'mts_posttop_adcode_time' => 'point_single_adcode_days',
		'mts_postend_adcode' => 'point_single_adcode_below',
		'mts_postend_adcode_time' => 'point_single_adcode_days_below',
	);
	
	$new_options = get_theme_mods();
	foreach ($migrate_options as $migrate_from => $migrate_to) {
		if ( isset( $old_options[$migrate_from] ) )
			$new_options[$migrate_to] = $old_options[$migrate_from];
	}
	update_option( 'theme_mods_point', $new_options );
	update_option( 'point_has_migrated', '1' );
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function point_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'point_content_width', 640 );
}
add_action( 'after_setup_theme', 'point_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function point_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'point' ),
		'id'            => 'sidebar',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'point_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function point_scripts() {
	wp_enqueue_style( 'point-style', get_stylesheet_uri() );
	wp_enqueue_script( 'jquery' );

	$handle = 'point-style';

	$point_rtl = get_theme_mod('point_rtl');
	if ( !empty($point_rtl) ) {
	    wp_enqueue_style( 'point_rtl', get_template_directory_uri() . '/css/rtl.css', 'point-style' );
	}

	wp_enqueue_script( 'point-customscripts', get_template_directory_uri() . '/js/customscripts.js', array(), '20120212', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	$point_color_scheme = get_theme_mod('point_color_scheme', '#38B7EE');
	$point_button_bg_color = get_theme_mod('point_button_bg_color', '#38B7EE');
	$header_textcolor = get_theme_mod('header_textcolor', '#555');
	$point_layout = get_theme_mod('point_layout', 'cslayout');
	$custom_css = "
        a:hover, .menu .current-menu-item > a, .menu .current-menu-item, .current-menu-ancestor > a.sf-with-ul, .current-menu-ancestor, footer .textwidget a, .single_post a, #commentform a, .copyrights a:hover, a, footer .widget li a:hover, .menu > li:hover > a, .single_post .post-info a, .post-info a, .readMore a, .reply a, .fn a, .carousel a:hover, .single_post .related-posts a:hover, .sidebar.c-4-12 .textwidget a, footer .textwidget a, .sidebar.c-4-12 a:hover, .title a:hover, .trending-articles li a:hover { color: $point_color_scheme; }
        .nav-previous a, .nav-next a, .sub-menu, #commentform input#submit, .tagcloud a, #tabber ul.tabs li a.selected, .featured-cat, .mts-subscribe input[type='submit'], .pagination a, .widget .wpt_widget_content #tags-tab-content ul li a, .latestPost-review-wrapper, .pagination .dots, .primary-navigation #wpmm-megamenu .wpmm-posts .wpmm-pagination a, #wpmm-megamenu .review-total-only { background: $point_color_scheme; color: #fff; } .header-button { background: $point_button_bg_color; } #logo a { color: #$header_textcolor; }
			";
	$custom_css .= get_theme_mod( 'custom_css', '' );
	if(!empty($point_layout) && $point_layout == "sclayout") {
		$custom_css .= ".article { float: right; } .sidebar.c-4-12 { float: left; }";
	}
	wp_add_inline_style( $handle, $custom_css );
}
add_action( 'wp_enqueue_scripts', 'point_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Add the 300x250 Ad Block Custom Widget.
 */
include_once( "functions/widget-ad300.php" );

/**
 * Add the 125x125 Ad Block Custom Widget.
 */
include_once( "functions/widget-ad125.php" );

/**
 * Add the Social buttons Widget.
 */
include_once( "functions/widget-social.php" );

/**
 * Recommended Plugins.
 */
include_once( "inc/plugin-activation.php" );

/**
 * Welcome Message.
 */
include_once( "inc/welcome-message.php" );

/**
 * RTL
 */
$point_rtl = get_theme_mod('point_rtl');
if ( !empty($point_rtl) ) {
    function mts_rtl() {
        global $wp_locale, $wp_styles;
        $wp_locale->text_direction = 'rtl';
    	if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
    		$wp_styles = new WP_Styles();
    		$wp_styles->text_direction = 'rtl';
    	}
    }
    add_action( 'init', 'mts_rtl' );
}

/**
 *Copyrights
 */
if ( ! function_exists( 'mts_copyrights_credit' ) ) {
	function mts_copyrights_credit() { 
	global $mts_options
?>
<!--start copyrights-->
<div class="copyrights">
	<div class="row" id="copyright-note">
		<?php
			$footer_logo = get_theme_mod('point_footer_logo');
			if ( $footer_logo != '' ) { ?>
			<div class="foot-logo">
				<a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo $footer_logo; ?>" alt="<?php bloginfo( 'name' ); ?>" ></a>
			</div>
		<?php } ?>
		<div class="copyright-left-text">Copyright &copy; <?php echo date("Y") ?> <a href="<?php echo home_url(); ?>" title="<?php bloginfo('description'); ?>" rel="nofollow"><?php bloginfo('name'); ?></a>.</div>
		<div class="copyright-text">
			<?php
				$copyright_text = get_theme_mod('copyright_text', 'Theme by <a href="http://mythemeshop.com/" rel="nofollow">MyThemeShop</a>.');
				echo $copyright_text;
			?>
		</div>
		<div class="footer-navigation">
			<?php if ( has_nav_menu( 'footer' ) ) { ?>
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_class' => 'menu', 'container' => '' ) ); ?>
			<?php } else { ?>
				<ul class="menu">
					<?php wp_list_pages('title_li='); ?>
				</ul>
			<?php } ?>
		</div>
		<div class="top"><a href="#top" class="toplink"><i class="point-icon icon-up-dir"></i></a></div>
	</div>
</div>
<!--end copyrights-->
<?php }
}

/**
 *Custom Comments template
 */
if ( ! function_exists( 'mts_comments' ) ) {
	function mts_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>" style="position:relative;" itemscope itemtype="http://schema.org/UserComments">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment->comment_author_email, 70 ); ?>
					<div class="comment-metadata">
					<?php printf('<span class="fn" itemprop="creator" itemscope itemtype="http://schema.org/Person">%s</span>', get_comment_author_link()) ?>
					<time><?php comment_date(get_option( 'date_format' )); ?></time>
					<span class="comment-meta">
						<?php edit_comment_link(__('(Edit)', 'point'),'  ','') ?>
					</span>
					<span class="reply">
						<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
					</span>
					</div>
				</div>
				<?php if ($comment->comment_approved == '0') : ?>
					<em><?php _e('Your comment is awaiting moderation.', 'point') ?></em>
					<br />
				<?php endif; ?>
				<div class="commentmetadata" itemprop="commentText">
					<?php comment_text() ?>
				</div>
			</div>
		</li>
	<?php }
}

/**
 * Short Post Title
 */
function mts_short_title($after = '', $length){
	$mytitle = get_the_title();
	if ( strlen($mytitle) > $length ){
		$mytitle = substr($mytitle,0,$length);
		echo $mytitle . $after; 
	}
	else { echo $mytitle; }
}

/*
 * Excerpt
 */
function mts_excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt);
  } else {
    $excerpt = implode(" ",$excerpt);
  }
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}

/**
 * Shorthand function to check for more tag in post.
 *
 * @return bool|int
 */
function mts_post_has_moretag() {
    return strpos( get_the_content(), '<!--more-->' );
}

if ( ! function_exists( 'mts_readmore' ) ) {
    /**
     * Display a "read more" link.
     */
    function mts_readmore() {
        ?>
        <div class="readMore">
            <a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
                <?php _e( 'Read More', 'point' ); ?>
            </a>
        </div>
        <?php 
    }
}

/*
 * Load Menu Description
 */
class mts_Walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '<br /><span class="sub">' . $item->description . '</span>';
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/*
 * Google Fonts
 */
function point_fonts_url() {
    $fonts_url = '';
 
    /* Translators: If there are characters in your language that are not
    * supported by Open Sans, translate this to 'off'. Do not translate
    * into your own language.
    */
    $droid = _x( 'on', 'Droid Sans font: on or off', 'point' );
 
    if ( 'off' !== $droid ) {
        $font_families = array();
 
        if ( 'off' !== $droid ) {
            $font_families[] = urldecode('Droid Sans:400,700');
        }
 
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            //'subset' => urlencode( 'latin,latin-ext' ),
        );
 
        $fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
    }
 
    return $fonts_url;
}

function point_scripts_styles() {
    wp_enqueue_style( 'theme-slug-fonts', point_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'point_scripts_styles' );

/**
 * Add `.primary-navigation` the WP Mega Menu's
 * @param $selector
 *
 * @return string
 */
function mts_megamenu_parent_element( $selector ) {
    return '.primary-navigation';
}
add_filter( 'wpmm_container_selector', 'mts_megamenu_parent_element' );