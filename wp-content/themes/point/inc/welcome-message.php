<?php
/**
 * Enqueue WP Pointer.
 */
function mts_point_pointer_header() {
    $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

    if ( ! in_array( 'mts_point_pointer', $dismissed ) ) { // mts_point_pointer
        add_action( 'admin_print_footer_scripts', 'mts_point_pointer_footer' );

        wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_style( 'wp-pointer' );
    }
}
add_action( 'admin_enqueue_scripts', 'mts_point_pointer_header' );

/**
 * Display WP Pointer configuration.
 */
function mts_point_pointer_footer() {
    $pointer_content = '<h3>'.__('Awesomeness!', 'point' ).'</h3>';
    $pointer_content .= '<p>'.__('You have just Installed Point WordPress Theme by MyThemeShop.', 'point' ).'</p>';
	$pointer_content .= '<p>'.wp_kses(__('You can Trigger The Awesomeness by clicking Amazing <strong>Customize</strong> link.', 'point' ), array( 'strong' => '' ) ).'</p>';

    $pointer_content .= '<p>'.__('If you face any problem, head over to', 'point' ).' <a href="https://community.mythemeshop.com/">'.__('Support Forums', 'point' ).'</a></p>';
?>
<script type="text/javascript">// <![CDATA[
jQuery(document).ready(function($) {
    $('.menu-icon-appearance ul li:nth-child(3)').pointer({
        content: '<?php echo wp_kses_post( $pointer_content ); ?>',
        position: {
            edge: 'left',
            align: 'center'
        },
        close: function() {
            $.post( ajaxurl, {
                pointer: 'mts_point_pointer', // mts_point_pointer
                action: 'dismiss-wp-pointer'
            });
        }
    }).pointer('open');
});
// ]]></script>
<?php
}

?>