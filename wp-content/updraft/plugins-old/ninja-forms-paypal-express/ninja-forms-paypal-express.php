<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - PayPal Express
 * Plugin URI: https://ninjaforms.com/extensions/paypal-express/
 * Description: Use PayPal Express to accept payments using your Ninja Forms.
 * Version: 3.0.2
 * Author: The WP Ninjas
 * Author URI: http://ninjaforms.com
 * Text Domain: ninja-forms-paypal-express
 *
 * Copyright 2013 The WP Ninjas.
 */

if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

    define("NINJA_FORMS_PAYPAL_EXPRESS_DIR", WP_PLUGIN_DIR."/".basename( dirname( __FILE__ ) ) . '/deprecated' );
    define("NINJA_FORMS_PAYPAL_EXPRESS_URL", plugins_url()."/".basename( dirname( __FILE__ ) ) . '/deprecated'  );
    define("NINJA_FORMS_PAYPAL_EXPRESS_VERSION", "3.0.2");
    define("NINJA_FORMS_PAYPAL_EXPRESS_DEBUG", false);

    include 'deprecated/paypal-express.php';

} else {

    include plugin_dir_path( __FILE__ ) . 'includes/deprecated.php';

    /**
     * Class NF_PayPalExpress
     */
    final class NF_PayPalExpress
    {
        const VERSION = '3.0.2';
        const SLUG    = 'paypal-express';
        const NAME    = 'PayPal Express';
        const AUTHOR  = 'The WP Ninjas';
        const PREFIX  = 'NF_PayPalExpress';

        /**
         * Plugin Instance
         *
         * @var NF_PayPalExpress
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';

        /**
         * API Connection
         *
         * @since 3.0
         * @var NF_PayPalExpress_Checkout
         */
        private $_api;

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_PayPalExpress Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_PayPalExpress)) {
                self::$instance = new NF_PayPalExpress();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                spl_autoload_register(array(self::$instance, 'autoloader'));
            }

            return self::$instance;
        }

        public function __construct()
        {
            add_action( 'admin_init', array( $this, 'setup_license') );

            add_action( 'ninja_forms_loaded', array( $this, 'setup_admin' ) );

            add_filter( 'ninja_forms_register_payment_gateways', array( $this, 'register_payment_gateways' ) );
        }

        /**
         * Setup Admin
         *
         * Setup admin classes for Ninja Forms and WordPress.
         */
        public function setup_admin()
        {
            Ninja_Forms()->merge_tags[ 'paypal_express' ] = new NF_PayPalExpress_MergeTags();

            if( ! is_admin() ) return;

            new NF_PayPalExpress_Admin_Settings();
            new NF_PayPalExpress_Admin_Metaboxes_Submission();
        }

        /**
         * Register Payment Gateways
         *
         * Register payment gateways with the Collect Payment action.
         *
         * @param array $payment_gateways
         * @return array $payment_gateways
         */
        public function register_payment_gateways($payment_gateways)
        {
            $payment_gateways[ 'paypal-express' ] = new NF_PayPalExpress_PaymentGateway();

            return $payment_gateways;
        }

        /**
         * API
         *
         * Setup PayPal Express API Connection
         *
         * @param bool $sandbox
         * @return NF_PayPalExpress_Checkout
         */
        public function api( $sandbox = FALSE )
        {
            if( ! $this->_api ) {

                if( $sandbox ){
                    $username = Ninja_Forms()->get_setting( 'ppe_test_api_username' );
                    $password = Ninja_Forms()->get_setting( 'ppe_test_api_password' );
                    $signature = Ninja_Forms()->get_setting( 'ppe_test_api_signature' );
                }else {
                    $username = Ninja_Forms()->get_setting( 'ppe_live_api_username' );
                    $password = Ninja_Forms()->get_setting( 'ppe_live_api_password' );
                    $signature = Ninja_Forms()->get_setting( 'ppe_live_api_signature' );
                }

                try {
                    $this->_api = new NF_PayPalExpress_Checkout( $username, $password, $signature, $sandbox );
                } catch (Exception $e) {
                    // TODO: Log Error, $e->getMessage(), for System Status Report
                }
            }
            return $this->_api;
        }

        /**
         * Autoloader
         *
         * Loads files using the class name to mimic the folder structure.
         *
         * @param $class_name
         */
        public function autoloader($class_name)
        {
            if (class_exists($class_name)) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }
        
        /**
         * Config
         *
         * @param $file_name
         * @return mixed
         */
        public static function config( $file_name )
        {
            return include self::$dir . 'includes/Config/' . $file_name . '.php';
        }

        /**
         * Template
         *
         * @param string $file_name
         * @param array $data
         */
        public static function template( $file_name = '', array $data = array() )
        {
            if( ! $file_name ) return;
            extract( $data );

            if( file_exists( self::$dir . 'includes/Templates/' . $file_name ) ) {
                include self::$dir . 'includes/Templates/' . $file_name;
            }
        }

        /**
         * Setup License
         */
        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }

    }

    /**
     * The main function responsible for returning The Highlander Plugin
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return {class} Highlander Instance
     */
    function NF_PayPalExpress()
    {
        return NF_PayPalExpress::instance();
    }

    // Go ninja, go ninja, go!
    NF_PayPalExpress();
}

add_filter( 'ninja_forms_upgrade_settings', 'NF_PayPalExpress_Upgrade' );
function NF_PayPalExpress_Upgrade( $data ){

    // Migrate plugin settings.
    $plugin_settings = get_option( 'ninja_forms_paypal', array(
        'currency' => 'USD',
        'live_api_user' => '',
        'live_api_pwd' => '',
        'live_api_signature' => '',
        'debug' => 0, // Copy over to per action setting.
        'test_api_user' => '',
        'test_api_pwd' => '',
        'test_api_signature' => ''
    ));

    Ninja_Forms()->update_settings( array(
        'ppe_currency' => $plugin_settings[ 'currency' ],
        'ppe_live_api_username' => $plugin_settings[ 'live_api_user' ],
        'ppe_live_api_password' => $plugin_settings[ 'live_api_pwd' ],
        'ppe_live_api_signature' => $plugin_settings[ 'live_api_signature' ],
        'ppe_test_api_username' => $plugin_settings[ 'test_api_user' ],
        'ppe_test_api_password' => $plugin_settings[ 'test_api_pwd' ],
        'ppe_test_api_signature' => $plugin_settings[ 'test_api_signature' ],
    ));

    // Convert form settings to action.
    if( isset( $data[ 'settings' ][ 'paypal_express' ] ) && 1 == $data[ 'settings' ][ 'paypal_express' ] ){

        $new_action = array(
            'type' => 'collectpayment',
            'label' => __( 'PayPal Express', 'ninja-forms-paypal-express' ),
            'payment_gateways' => 'paypal-express',
            'ppe_description' => '',
        );

        /*
         * Payment Total
         */

        if( isset( $data[ 'settings' ][ 'paypal_default_total' ] ) && $data[ 'settings' ][ 'paypal_default_total' ] ) {
            $new_action[ 'payment_total' ] = $data[ 'settings' ][ 'paypal_default_total' ];
        }

        foreach( $data[ 'fields' ] as $field ){

            if( '_calc' != $field[ 'type' ] ) continue;
            if( ! isset( $field[ 'data' ][ 'calc_name' ] ) || 'total' != $field[ 'data' ][ 'calc_name' ] ) continue;

            $new_action[ 'payment_total' ] = '{field:' . $field[ 'id' ] . '}';
        }

        /*
         * Note to Buyer
         *
         * Change: Product Name + Product Description => Description (Note to Buyer)
         */

        if( isset( $data[ 'settings' ][ 'paypal_product_name' ] ) && $data[ 'settings' ][ 'paypal_product_name' ] ) {
            $new_action[ 'ppe_description' ][] = $data[ 'settings' ][ 'paypal_product_name' ];
        }

        if( isset( $data[ 'settings' ][ 'paypal_product_desc' ] ) && $data[ 'settings' ][ 'paypal_product_desc' ] ) {
            $new_action[ 'ppe_description' ][] = $data[ 'settings' ][ 'paypal_product_desc' ];
        }

        $new_action[ 'ppe_description' ] = implode( ': ', $new_action[ 'ppe_description' ] );

        /*
         * Sandbox and Debug Mode
         *
         * Change: Modes are now per action settings.
         * Rename: Debug Mode -> Sandbox Mode (Use Sandbox Credentials)
         * Rename: Test Mode  -> Debug Mode (Debug the Response)
         */

        if( $plugin_settings[ 'debug' ] ){
            $new_action[ 'ppe_sandbox' ] = 1;
        }

        if( isset( $data[ 'settings' ][ 'paypal_test_mode' ] ) && $data[ 'settings' ][ 'paypal_test_mode' ] ) {
            $new_action[ 'ppe_debug' ] = 1;
        }


        $data[ 'actions' ][] = $new_action;
    }

    return $data;
}
