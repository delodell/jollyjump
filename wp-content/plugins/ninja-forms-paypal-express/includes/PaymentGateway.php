<?php if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'NF_Abstracts_PaymentGateway' ) ) return;

/**
 * The PayPal Express payment gateway for the Collect Payment action.
 */
class NF_PayPalExpress_PaymentGateway extends NF_Abstracts_PaymentGateway
{
    protected $_slug = 'paypal-express';

    public function __construct()
    {
        parent::__construct();

        $this->_name = __( 'PayPal Express', 'ninja-forms-paypal-express' );

        add_action( 'ninja_forms_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        /*
        |--------------------------------------------------------------------------
        | Product Description
        |--------------------------------------------------------------------------
        */

        $this->_settings[ 'ppe_description' ] = array(
            'name' => 'ppe_description',
            'type' => 'textbox',
            'label' => __( 'Note to Buyer', 'ninja-forms' ),
            'width' => 'full',
            'group' => 'advanced',
            'deps'  => array(
                'payment_gateways' => $this->_slug
            ),
            'help' => sprintf( __( 'A note from the merchant to the buyer that will be displayed in the PayPal checkout window. Limit %s characters', 'ninja-forms-paypal-express' ), '165' ),
            'use_merge_tags' => TRUE
        );

        $this->_settings[ 'ppe_sandbox' ] = array(
            'name' => 'ppe_sandbox',
            'type' => 'toggle',
            'label' => __( 'Sandbox Mode', 'ninja-forms' ),
            'width' => 'full',
            'group' => 'advanced',
            'deps'  => array(
                'payment_gateways' => $this->_slug
            ),
            'help' => __( 'Use PayPal Express sandbox credentials to test transaction.', 'ninja-forms-paypal-express' ),
        );

        $this->_settings[ 'ppe_debug' ] = array(
            'name' => 'ppe_debug',
            'type' => 'toggle',
            'label' => __( 'Debug Mode', 'ninja-forms' ),
            'width' => 'full',
            'group' => 'advanced',
            'deps'  => array(
                'payment_gateways' => $this->_slug,
                'ppe_sandbox' => 1
            ),
            'help' => __( 'Displays the response from PayPal. Does NOT complete the transaction.', 'ninja-forms-paypal-express' ),
        );
    }

    /**
     * Process
     *
     * The main function for processing submission data.
     *
     * @param array $action_settings Action specific settings.
     * @param int $form_id The ID of the submitted form.
     * @param array $data Form submission data.
     * @return array $data Modified submission data.
     */
    public function process( $action_settings, $form_id, $data )
    {
        if( 1 != $action_settings[ 'ppe_sandbox' ] ) $action_settings[ 'ppe_sandbox' ] = FALSE; // `1` == TRUE

        $api = NF_PayPalExpress()->api( $action_settings[ 'ppe_sandbox' ] );
        $payment_total = number_format( $action_settings[ 'payment_total' ], 2, '.', ',' );

        if( isset( $data[ 'resume' ] ) ){

            $currency = $this->get_currency( $data );
            $token = $data[ 'resume' ][ 'token' ];
            $payer_id = $data[ 'resume' ][ 'PayerID' ];
            $response = $api->complete_checkout( $payment_total, $currency, $token, $payer_id );

            if ( $this->is_success( $response ) ) { //Request successfully

                $this->update_submission( $this->get_sub_id($data), array(
                    'paypal_status' => $this->get_status( 'success' ),
                    'paypal_transaction_id' => $response['PAYMENTINFO_0_TRANSACTIONID'],
                ));

                Ninja_Forms()->merge_tags[ 'paypal_express' ]->set_transaction_id( $response['PAYMENTINFO_0_TRANSACTIONID'] );

                do_action( 'ninja_forms_checkout_success', $response );
                do_action( 'ninja_forms_paypal_express_checkout_success', $response );

            } else {
                $data[ 'errors' ][ 'form' ][] = __( 'PayPal encountered an error in processing your transaction. Please try again.', 'ninja-forms-paypal-express' );

                do_action( 'ninja_forms_checkout_failure', $response );
                do_action( 'ninja_forms_paypal_express_checkout_failure', $response );
            }

            $data[ 'actions' ][ 'paypal_express' ][ 'ppe_debug' ] = $action_settings[ 'ppe_debug' ];
            if( isset( $action_settings[ 'ppe_debug' ] ) && 1 == $action_settings[ 'ppe_debug' ] ){ // `1` == TRUE
                $data[ 'actions' ][ 'paypal_express' ][ 'debug' ] = $response;

                $debug_message = '<dl>';
                foreach( $response as $key => $value ){
                    $debug_message .= "<dt>$key</dt><dd>$value</dd>";
                }
                $debug_message .= '</dl>';
                $data[ 'debug' ][ 'paypal_express' ] = $debug_message;
            }

            return $data;
        }

        $currency = $this->get_currency( $data );
        $description = wp_strip_all_tags( $action_settings[ 'ppe_description' ] );
        $response = $debug[ 'response' ][] = $api->checkout( $payment_total, $currency, $form_id, $description );

        if ( $this->is_success( $response ) ) { //Request successfully
            $token = $debug[ 'checkout_token' ][] = $response['TOKEN'];

            // Set Checkout (Redirect) URL
            $data[ 'halt' ] = TRUE;
            $data[ 'actions' ][ 'redirect' ] = $api->get_checkout_url( $token );

            $this->update_submission( $this->get_sub_id( $data ), array(
                'paypal_status' => __( 'Pending', 'ninja-forms-paypal-express' ),
                'paypal_total' => $payment_total
            ) );

        } else {
            $data[ 'errors' ][] = __( 'PayPal encountered an error in processing your transaction. Please try again.', 'ninja-forms-paypal-express' );
        }

        return $data;
    }

    public function enqueue_scripts( $data )
    {
        // TODO: Check `$data[ 'form_id' ]`
        wp_enqueue_script('nf-paypal-express-debug', NF_PayPalExpress::$url . 'assets/js/debug.js', array( 'nf-front-end' ) );
    }

    /**
     * Is Success
     *
     * @param array $response
     * @return bool
     */
    private function is_success( $response )
    {
        if( ! is_array( $response ) ) return FALSE;

        if( ! in_array( $response[ 'ACK' ], array( 'Success', 'SuccessWithWarning' ) ) ) return FALSE;

        return TRUE;
    }

    /**
     * Update Submission
     *
     * @param int $sub_id
     * @param array $data
     */
    private function update_submission( $sub_id, $data = array() )
    {
        if( ! $sub_id ) return;
        
        $sub = Ninja_Forms()->form()->sub( $sub_id )->get();

        foreach( $data as $key => $value ){
            $sub->update_extra_value( $key, $value );
        }

        $sub->save();
    }

    /**
     * Get Submission ID
     *
     * Get the submission id from the submission data, if it exists.
     *
     * @param array $data
     * @return int|bool
     */
    private function get_sub_id( $data )
    {
        if( isset( $data[ 'actions' ][ 'save' ][ 'sub_id' ] ) ){
            return $data[ 'actions' ][ 'save' ][ 'sub_id' ];
        }
        return FALSE;
    }

    private function get_status( $status )
    {
        $lookup = array(
            'pending' => __( 'Pending', 'ninja-forms-paypal-express' ),
            'cancel'  => __( 'Cancelled', 'ninja-forms-paypal-express' ),
            'success' => __( 'Completed', 'ninja-forms-paypal-express' ),
        );

        return ( isset( $lookup[ $status ] ) ) ? $lookup[ $status ] : $lookup[ 'pending' ];
    }

    private function get_currency( $form_data )
    {
        /**
         * Currency Setting Priority
         *
         * 3. Paypal Express Currency Setting (deprecated)
         * 2. Ninja Forms Currency Setting
         * 1. Form Currency Setting (default)
         */
        $ppe_currency = Ninja_Forms()->get_setting( 'ppe_currency', 'USD' );
        $plugin_currency = Ninja_Forms()->get_setting( 'currency', $ppe_currency );
        $form_currency   = ( isset( $form_data[ 'settings' ][ 'currency' ] ) ) ? $form_data[ 'settings' ][ 'currency' ] : $plugin_currency;
        return $form_currency;
    }

} // END CLASS NF_PayPalExpress_PaymentGateway