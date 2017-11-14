<?php
/*
 * Add custom endpoint that appears in My Account Page - WooCommerce 2.6
 * New URL below as Claudio changed his github username
 * Ref - https://gist.github.com/claudiosanches/a79f4e3992ae96cb821d3b357834a005#file-custom-my-account-endpoint-php
 */
function cash_transaction_lines( $user_id ) {
    $lines = array();
    foreach( get_user_meta( $user_id, 'cash_transaction', false ) as $movement ) {
	$lines[] = json_decode( $movement, true ); # true = as associative array
    }
    return $lines;
}

class Cash_My_Account_Endpoint {
    /**
     * Custom endpoint name.
     *
     * @var string
     */
    public static $endpoint = 'cash-endpoint';
    /**
     * Plugin actions.
     */
    public function __construct() {
	// Actions used to insert a new endpoint in the WordPress.
	add_action( 'init', array( $this, 'add_endpoints' ) );
	add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
	// Change the My Account page title.
	add_filter( 'the_title', array( $this, 'endpoint_title' ) );
	// Insering your new tab/page into the My Account page.
	add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
	add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array( $this, 'endpoint_content' ) );
	$GLOBALS['hook_suffix'] = '';
    }
    /**
     * Register new endpoint to use inside My Account page.
     *
     * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
     */
    public function add_endpoints() {
	add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
    }
    /**
     * Add new query var.
     *
     * @param array $vars
     * @return array
     */
    public function add_query_vars( $vars ) {
	$vars[] = self::$endpoint;
	return $vars;
    }
    /**
     * Set endpoint title.
     *
     * @param string $title
     * @return string
     */
    public function endpoint_title( $title ) {
	global $wp_query;
	$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );
	if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
	    // New page title.
	    $title = __( 'Cash', 'myc' );
	    remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
	}
	return $title;
    }
    /**
     * Insert the new endpoint into the My Account menu.
     *
     * @param array $items
     * @return array
     */
    public function new_menu_items( $items ) {
	// Remove the logout menu item.
	$logout = $items['customer-logout'];
	unset( $items['customer-logout'] );
	// Insert your custom endpoint.
	$items[ self::$endpoint ] = __( 'Cash', 'myc' );
	// Insert back the logout item.
	$items['customer-logout'] = $logout;
	return $items;
    }

    /**
     * Endpoint HTML content.
     */
    public function endpoint_content() {
?>

    <div class="woocommerce-MyAccount-content">

	<div class="options_group">
	    <p class="form-field ">
		<div id="cash-transactions-wrapper" class="wrap">
		    <?php
		    function is_user_myc_admin( $user_id ) {
			return isset( get_user_meta( $user_id, 'myc_capabilities' )[0]['administrator'] );
		    }
		    if ( is_user_myc_admin( get_current_user_id() ) ): ?>
			<select name="cu" id="choose_user">
			    <option value="" selected disabled><?php echo __( 'Choose user:', 'myc' ); ?></option>
			    <?php
			    global $wpdb;
			    foreach( $wpdb->get_results(
				"SELECT ID, user_nicename, user_email FROM {$wpdb->prefix}users ORDER BY ID ASC"
			    ) as $user ) {
				printf( "<option value='%s'>%s %s</option>\n", $user->ID, $user->user_nicename, $user->user_email );
			    }
			    ?>
			</select>
			<div id="transaction-table-data">
			</div>
			<div id="transaction-table-button" class="form-field _make_deposit" style="display:none">
			    <h4><?php echo __( 'Make Deposit', 'myc' );?></h4>
			    <input type="text" class="make_deposit" id="make_deposit_input"/>
			    <button class="button" id="make-deposit-button"><?php echo __( 'Deposit', 'myc' )?></button>
			    <select name="deposit-type-selector" id="deposit-type-selector">
				<option value="" selected disabled><?php echo __( 'Type', 'myc' ); ?></option>
				<?php
				$id = get_term_by( 'name', 'deposit_type', 'category' )->term_id;
				foreach ( get_term_meta( $id )[ 'deposit_type' ] as $deposit_type_str ) {
				    $deposit_type = json_decode( $deposit_type_str );
				    if ( 'cash' === $deposit_type->description  ) {
					echo '<option value="cash" selected>' . __( 'Cash', 'myc' );
				    } else {
					echo '<option value="' . str_replace( "\"", "_", $deposit_type_str ) . '">' . $deposit_type->description . ' @ ' . $deposit_type->rate . ' E/hr';
				    }
				    echo '</option>';
				}
				?>
			    </select>
			</div>
		    <?php else:?>
			<br>
			<h4>
			    <?php echo __( 'Current balance:', 'myc' ) . ' ' . number_format((float) get_last_balance( get_current_user_id() ), 2, '.', ''); ?>
			</h4>
		    <?php 
		    $user_id = get_current_user_id(); 
		    $table = new MYC_Cash_Transactions( cash_transaction_lines( $user_id ) );
		    $table->prepare_items();
		    $table->display();
		    endif; ?>
		</div>
	    </p>
	</div>

<?php
}
/**
 * Plugin install action.
 * Flush rewrite rules to make our custom endpoint available.
 */
public static function install() {
    flush_rewrite_rules();
}
}
new Cash_My_Account_Endpoint();
// Flush rewrite rules on plugin activation.
register_activation_hook( __FILE__, array( 'Cash_My_Account_Endpoint', 'install' ) );

add_action( 'wp_footer', function() {?>
    <script type="text/javascript">
     function display_cash_transactions() {
	 var user_id = jQuery( '#choose_user' ).val();
	 if ( null == user_id ) {
	     return;
	 }
	 jQuery.post( ajaxurl, {
	     'action' : 'show_user_cash_transactions',
	     'user_id': user_id,
	     '_nonce' : '<?php echo wp_create_nonce( 'show_user_cash_transactions' ) ?>'
	 },
		      function( response ) {
			  jQuery( '#transaction-table-data' ).html(response);
			  jQuery( '#transaction-table-button' ).show();
		      });
     }
     jQuery(document).ready(function() {
	 jQuery( '#choose_user ' ).change( function() {
	     display_cash_transactions();
	 });
	 jQuery( '#make-deposit-button' ).click( function() {
	     jQuery.post( ajaxurl, {
		 'action' : 'make_deposit',
		 'user_id': jQuery( '#choose_user' ).val(),
		 'qty'    : jQuery( '#make_deposit_input' ).val(),
		 'type'   : jQuery( '#deposit-type-selector' ).val(),
		 '_nonce' : '<?php echo wp_create_nonce( 'make_deposit' ); ?>'
	     },
			  function( response ) {
			      jQuery( '#make_deposit_input' ).val( '' );
			      jQuery( '#deposit-type-selector' ).val( 'cash' );
			      display_cash_transactions();
			  });
	 });
     });
    </script>
<?php
});

add_action( 'wp_ajax_show_user_cash_transactions', function() {
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'show_user_cash_transactions' ) ) {
	wp_die( "Don't mess with me!" );
    }
    $user_id = $_POST[ 'user_id' ];
    if ( null == $user_id ) {
	wp_die();
    }
    echo '<br><h4>' . __( 'Current balance:', 'myc' ) . ' ' . number_format((float) get_last_balance( $user_id ), 2, '.', '') . '</h4>';
    $table = new MYC_Cash_Transactions( cash_transaction_lines( $user_id ) );
    $table->prepare_items();
    $table->display();
    wp_die();
});

add_action( 'wp_ajax_make_deposit', function() {
    error_log("in ajax");
    if ( ! wp_verify_nonce( $_POST[ '_nonce' ], 'make_deposit' ) ) {
	wp_die( "Don't mess with me!" );
    }
    error_log("post: " . var_export($_POST,1));
    $user_id = $_POST[ 'user_id' ];
    $qty = (float) $_POST[ 'qty' ];
    $type = json_decode( str_replace( "_", "\"", $_POST[ 'type' ] ) );

    $last_balance = (float) get_last_balance( $user_id );
    $total = $qty * (float) $type->rate;
    add_user_meta( $user_id, 'cash_transaction', json_encode( array( 
	'id' => '',
	'date' => date( 'Y-m-d', strtotime('now') ),
	'balance_before' => $last_balance,
	'money_qty' => $total,
	'balance_after' => $last_balance + $total,
	'transaction_type' => ( 'cash' === $type->description
			      ? $type->description
			      : $type->description . ' @ ' . $type->rate . ' E/hr' )
    ) ) );
    wp_die();
});
