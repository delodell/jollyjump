<?php
/**
 * Sample implementation of the Custom Header feature.
 *
 * You can add an optional custom header image to header.php like so ...
 *
 * @link http://codex.wordpress.org/Custom_Headers
 *
 * @package point
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses point_header_style()
 * @uses point_admin_header_style()
 * @uses point_admin_header_image()
 */
function point_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'point_custom_header_args', array(
		'default-image'          => '',
		'default-text-color'     => '2a2a2a',
		'flex-width'			 => true,
		'flex-height'            => true,
		'wp-head-callback'       => 'point_header_style',
		'admin-head-callback'    => 'point_admin_header_style',
		'admin-preview-callback' => 'point_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'point_custom_header_setup' );

if ( ! function_exists( 'point_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see point_custom_header_setup().
 */
function point_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value.
	if ( HEADER_TEXTCOLOR === $header_text_color ) {
		return;
	}

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
		<?php // Has the text been hidden
		if ( ! display_header_text() ) : ?>
			.site-title, .site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		<?php
		// If the user has set a custom color for the text use that.
		else : ?>
			.site-title a,
			.site-description {
				color: #<?php echo esc_attr( $header_text_color ); ?>;
			}
		<?php endif; ?>
		</style>
	<?php
}
endif; // point_header_style

if ( ! function_exists( 'point_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see point_custom_header_setup().
 */
function point_admin_header_style() { ?>
	<style type="text/css">
		.appearance_page_custom-header #headimg { border: none; }
	</style>
<?php
}
endif; // point_admin_header_style

if ( ! function_exists( 'point_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see point_custom_header_setup().
 */
function point_admin_header_image() {
?>
	<div id="headimg">
		<h1 class="displaying-header-text">
			<a id="name" style="<?php echo esc_attr( 'color: #' . get_header_textcolor() ); ?>" onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		</h1>
		<div class="displaying-header-text" id="desc" style="<?php echo esc_attr( 'color: #' . get_header_textcolor() ); ?>"><?php bloginfo( 'description' ); ?></div>
		<?php if ( get_header_image() ) : ?>
			<img src="<?php header_image(); ?>" alt="">
		<?php endif; ?>
	</div>
<?php
}
endif; // point_admin_header_image