<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( dirname(__FILE__) . '/../../woocommerce/includes/emails/class-wc-email.php' );

class MYC_Order_Now_Email extends WC_Email {

    public function __construct() {
	
	$this->title = __( 'Order Now Email', 'myc' );
	$this->description = __( 'Order Now Emails are sent by you to advise users that they may place their order', 'myc' );
	$this->subject = __( 'Order Now Email', 'myc' );
	$this->heading = __( 'Week 8 Summer', 'myc' );
	$this->target = '';
	
	// these define the locations of the templates that this email should use
	$this->template_html  = 'emails/admin-order-now.php';
	$this->template_plain = 'emails/plain/admin-order-now.php';

	parent::__construct();
    }

    public function process_admin_options() {
	parent::process_admin_options();
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @param int $order_id
     */
    public function trigger() {
	global $wpdb;
	$query = "SELECT u.user_email FROM {$wpdb->usermeta} um LEFT JOIN {$wpdb->users} u ON um.user_id=u.id WHERE um.meta_key='{$wpdb->prefix}capabilities' AND um.meta_value='" . serialize( array( __( $this->target, 'myc' ) => true ) ) . "'";

	$this->recipient = implode( ',', $wpdb->get_col( $query ) );

	require_once( dirname(__FILE__) . '/../../woocommerce/includes/libraries/class-emogrifier.php' );
	return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
	ob_start();
	wc_get_template( $this->template_html, array(
	    'email'         => $this->object,
	    'email_heading' => $this->get_heading(),
	    'target_tag'    => ( 'user' === $this->target ? 'per_individuals' : 'per_coopes' )
	) );
	return ob_get_clean();
    }

    public function get_content_plain() {
	ob_start();
	woocommerce_get_template( $this->template_plain, array(
	    'order'         => $this->object,
	    'email_heading' => $this->get_heading()
	) );
	return ob_get_clean();
    }

    public function init_form_fields() {
 	$this->form_fields = array(
	    'enabled'    => array(
		'title'   => 'Enable/Disable',
		'type'    => 'checkbox',
		'label'   => __( 'Enable this email notification', 'myc' ),
		'default' => 'yes'
	    ),
	    'send_now'    => array(
		'title'   => __( 'Send now', 'myc' ),
		'type'    => 'button',
		'label'   => __( 'Send this email notification', 'myc' ),
		'default' => __( 'Send', 'myc' )
	    ),
	    'subject'    => array(
		'title'       => 'Subject',
		'type'        => 'text',
		'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'myc' ), $this->subject ),
		'placeholder' => '',
		'default'     => ''
	    ),
	    'heading'    => array(
		'title'       => 'Email Heading',
		'type'        => 'text',
		'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'myc' ), $this->heading ),
		'placeholder' => '',
		'default'     => ''
	    ),
	    'email_type' => array(
		'title'       => 'Email type',
		'type'        => 'select',
		'description' => 'Choose which format of email to send.',
		'default'     => 'html',
		'class'       => 'email_type',
		'options'     => array(
		    'plain'     => 'Plain text',
		    'html'      => 'HTML', 'woocommerce',
		    'multipart' => 'Multipart', 'woocommerce',
		)
	    )
	);
    }
    
} // end MYC_Order_Now_Email


class MYC_User_Order_Now_Email extends MYC_Order_Now_Email {

    public function __construct() {

	// Trigger on "send" event
	//	add_action( 'woocommerce_order_status_failed_to_processing_notification',  array( $this, 'trigger' ) );
	
	parent::__construct();
	$this->id = 'myc_user_order_now_email';
	$this->title = __( 'User Order Now Email', 'myc' );
	$this->heading = __( 'User Order Now Email', 'myc' );
	$this->target = __( 'user', 'myc' );
	$this->subject = __( 'User Order Now Email', 'myc' );
	$this->init_settings();
	$this->settings['send_now'] = __( 'Send', 'myc' );
	add_action( 'woocommerce_update_options_email_' . $this->id, array( $this, 'process_admin_options' ) );
    }
} // end MYC_User_Order_Now_Email

class MYC_Coopes_Order_Now_Email extends MYC_Order_Now_Email {

    public function __construct() {
	parent::__construct();
	$this->id = 'myc_coopes_order_now_email';
	$this->title = __( 'Coopes Order Now Email', 'myc' );
	$this->heading = __( 'Coopes Order Now Email', 'myc' );
	$this->target = __( 'coope', 'myc' );
	$this->init_settings();
	$this->settings['send_now'] = __( 'Send', 'myc' );
	add_action( 'woocommerce_update_options_email_' . $this->id, array( $this, 'process_admin_options' ) );
    }

} // end MYC_Coopes_Order_Now_Email

class MYC_Order_Reminder_Email extends WC_Email {

    public function __construct() {
	
	$this->title = __( 'Order Reminder Email', 'myc' );
	$this->description = __( 'Order Reminder Emails are sent on Tuesdays at 14:00 to remind users to place their order', 'myc' );
	$this->heading = __( 'Order Reminder Email', 'myc' );
	$this->subject = __( 'Dont forget to order!', 'myc' );
	
	// these define the locations of the templates that this email should use
	$this->template_html  = 'emails/admin-reminder-to-order.php';
	$this->template_plain = 'emails/plain/admin-reminder-to-order.php';
	
	// Trigger on new paid orders
	//	add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
	//	add_action( 'woocommerce_order_status_failed_to_processing_notification',  array( $this, 'trigger' ) );
	
	parent::__construct();
    }

    public function trigger() {
	global $wpdb;
	$query = "SELECT u.user_email FROM {$wpdb->usermeta} um LEFT JOIN {$wpdb->users} u ON um.user_id=u.id WHERE um.meta_key='{$wpdb->prefix}capabilities' AND um.meta_value='" . serialize( array( __( $this->target, 'myc' ) => true ) ) . "'";

	$this->recipient = implode( ',', $wpdb->get_col( $query ) );

	require_once( dirname(__FILE__) . '/../../woocommerce/includes/libraries/class-emogrifier.php' );
	return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
	ob_start();
	wc_get_template( $this->template_html, array(
	    'email'         => $this->object,
	    'email_heading' => $this->get_heading(),
	    'target_tag'    => ( 'user' === $this->target ? 'per_individuals' : 'per_coopes' )
	) );
	return ob_get_clean();
    }

    public function get_content_plain() {
	ob_start();
	woocommerce_get_template( $this->template_plain, array(
	    'order'         => $this->object,
	    'email_heading' => $this->get_heading()
	) );
	return ob_get_clean();
    }

    public function init_form_fields() {
 	$this->form_fields = array(
	    'enabled'    => array(
		'title'   => 'Enable/Disable',
		'type'    => 'checkbox',
		'label'   => __( 'Enable this email notification', 'myc' ),
		'default' => 'yes'
	    ),
	    'subject'    => array(
		'title'       => 'Subject',
		'type'        => 'text',
		'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'myc' ), $this->subject ),
		'placeholder' => '',
		'default'     => ''
	    ),
	    'heading'    => array(
		'title'       => 'Email Heading',
		'type'        => 'text',
		'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'myc' ), $this->heading ),
		'placeholder' => '',
		'default'     => ''
	    ),
	    'email_type' => array(
		'title'       => 'Email type',
		'type'        => 'select',
		'description' => 'Choose which format of email to send.',
		'default'     => 'html',
		'class'       => 'email_type',
		'options'     => array(
		    'plain'     => 'Plain text',
		    'html'      => 'HTML', 'woocommerce',
		    'multipart' => 'Multipart', 'woocommerce',
		)
	    )
	);
    }
    
} // end MYC_Order_Reminder_Email

class MYC_User_Order_Reminder_Email extends MYC_Order_Reminder_Email {

    public function __construct() {
	parent::__construct();
	$this->id = 'myc_user_order_reminder_email';
	$this->title = __( 'User Order Reminder Email', 'myc' );
	$this->heading = __( 'User Order Reminder Email', 'myc' );
	$this->target = 'user';
    }
} // end MYC_User_Order_Reminder_Email

class MYC_Coopes_Order_Reminder_Email extends MYC_Order_Reminder_Email {

    public function __construct() {
	parent::__construct();
	$this->id = 'myc_coopes_order_reminder_email';
	$this->title = __( 'Coopes Order Reminder Email', 'myc' );
	$this->heading = __( 'Coopes Order Reminder Email', 'myc' );
	$this->subject = __( 'Coopes Order Reminder Email', 'myc' );
	$this->target = 'coope';
    }
} // end MYC_Coopes_Order_Reminder_Email
