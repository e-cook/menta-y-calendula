<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( dirname(__FILE__) . '/../../woocommerce/includes/emails/class-wc-email.php' );

class MYC_Order_Now_Email extends WC_Email {

    public function __construct() {
	
	$this->id = 'myc_order_now_email';
	$this->title = __( 'Order Now Email', 'myc' );
	$this->description = __( 'Order Now Emails are sent by you to advise users that they may place their order', 'myc' );
	$this->heading = __( 'Order Now Email', 'myc' );
	$this->subject = __( 'Order Now Email', 'myc' );
	
	// these define the locations of the templates that this email should use
	$this->template_html  = 'emails/admin-order-now.php';
	$this->template_plain = 'emails/plain/admin-order-now.php';
	
	parent::__construct();
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @param int $order_id
     */
    public function trigger( $target ) {

	// replace variables in the subject/headings
	//	$this->find[] = '{' . $target . '}';
	//	$this->replace[] = __( '{' . $target . '}', 'myc' );

	global $wpdb;
	$recipients = $wpdb->get_col( "SELECT u.user_email FROM {$wpdb->usermeta} um LEFT JOIN {$wpdb->users} u ON um.user_id=u.id WHERE um.meta_key='{$wpdb->prefix}capabilities' AND um.meta_value='" . serialize( array( __( $target, 'myc' ) => true ) ) . "'" );
	// woohoo, send the email!
	//	$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		error_log( "sending " . var_export(array( $recipients, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ),1));
    }

    public function get_content_html() {
	ob_start();
	wc_get_template( $this->template_html, array(
	    'order'         => $this->object,
	    'email_heading' => $this->get_heading()
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
	$this->subject = __( 'User Order Now Email', 'myc' );
    }

} // end MYC_User_Order_Now_Email

class MYC_Coopes_Order_Now_Email extends MYC_Order_Now_Email {

    public function __construct() {
	parent::__construct();
	$this->id = 'myc_coopes_order_now_email';
	$this->title = __( 'Coopes Order Now Email', 'myc' );
	$this->heading = __( 'Coopes Order Now Email', 'myc' );
	$this->subject = __( 'Coopes Order Now Email', 'myc' );
    }
} // end MYC_Coopes_Order_Now_Email

class MYC_Order_Reminder_Email extends WC_Email {

    public function __construct() {
	
	$this->id = 'myc_order_reminder_email';
	$this->title = __( 'Order Reminder Email', 'myc' );
	$this->description = __( 'Order Reminder Emails are sent on Tuesdays at 14:00 to remind users to place their order', 'myc' );
	$this->heading = __( 'Order Reminder Email', 'myc' );
	$this->subject = __( 'Order Reminder Email', 'myc' );
	
	// these define the locations of the templates that this email should use
	$this->template_html  = 'emails/admin-reminder-to-order.php';
	$this->template_plain = 'emails/plain/admin-reminder-to-order.php';
	
	// Trigger on new paid orders
	//	add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
	//	add_action( 'woocommerce_order_status_failed_to_processing_notification',  array( $this, 'trigger' ) );
	
	parent::__construct();
    }

    /**
     * Determine if the email should actually be sent and setup email merge variables
     *
     * @param int $order_id
     */
    public function trigger( $order_id ) {
	
	// bail if no order ID is present
	if ( ! $order_id )
	    return;
	
	// setup order object
	$this->object = new WC_Order( $order_id );
	
	// bail if shipping method is not expedited
	if ( ! in_array( $this->object->get_shipping_method(), array( 'Three Day Shipping', 'Next Day Shipping' ) ) )
	    return;
	
	// replace variables in the subject/headings
	$this->find[] = __( '{users}', 'myc' );
	$this->replace[] = 'THE_USERS';
	
	$this->find[] = '{order_number}';
	$this->replace[] = $this->object->get_order_number();
	
	if ( ! $this->is_enabled() || ! $this->get_recipient() )
	    return;
	
	// woohoo, send the email!
	//	$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	error_log( "sending ", var_export(array( $recipients, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ),1));
    }

    public function get_content_html() {
	ob_start();
	woocommerce_get_template( $this->template_html, array(
	    'order'         => $this->object,
	    'email_heading' => $this->get_heading()
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
	$this->subject = __( 'User Order Reminder Email', 'myc' );
    }
} // end MYC_User_Order_Reminder_Email

class MYC_Coopes_Order_Reminder_Email extends MYC_Order_Reminder_Email {

    public function __construct() {
	parent::__construct();
	$this->id = 'myc_coopes_order_reminder_email';
	$this->title = __( 'Coopes Order Reminder Email', 'myc' );
	$this->heading = __( 'Coopes Order Reminder Email', 'myc' );
	$this->subject = __( 'Coopes Order Reminder Email', 'myc' );
    }
} // end MYC_Coopes_Order_Reminder_Email
