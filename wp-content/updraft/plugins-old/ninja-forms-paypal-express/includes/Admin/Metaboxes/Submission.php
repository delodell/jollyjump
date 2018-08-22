<?php if ( ! defined( 'ABSPATH' ) ) exit;

final class NF_PayPalExpress_Admin_Metaboxes_Submission extends NF_Abstracts_SubmissionMetabox
{
    public function __construct()
    {
        parent::__construct();

        $this->_title = __( 'Payment Details', 'ninja-forms' );

        if( $this->sub && ! $this->sub->get_extra_value( 'paypal_status' ) ){
            remove_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        }
    }

    public function render_metabox( $post, $metabox )
    {
        $data = array(
            __( 'Status', 'ninja-forms-paypal-express' ) => $this->sub->get_extra_value( 'paypal_status' ),
            __( 'Total', 'ninja-forms-paypal-express' )  => $this->sub->get_extra_value( 'paypal_total' ),
            __( 'Transaction ID', 'ninja-forms-paypal-express' ) => $this->sub->get_extra_value( 'paypal_transaction_id' )
        );

        NF_PayPalExpress::template( 'admin-metaboxes-submission.html.php', $data );
    }
}