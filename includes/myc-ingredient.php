<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'woocommerce_product_data_panels', 'render_latest_purchases' );
function render_latest_purchases() {
    global $post;
    $product = wc_get_product( $post );
    if ( 'ingredient' != $product->get_type() ) {
	return;
    }
?>
<div id='purchases_options' class='panel woocommerce_options_panel'>
    <div class="options_group">
	<p class="form-field _latest_purchases">
	    <span class="wrap">
		<h4 for="_latest_purchases"><?= __( 'Latest Purchases' )?></h4>
		<?php

		$table = new MYC_Latest_Purchases( $product->latest_purchases() );
		$table->prepare_items();
		$table->display();

		//		    echo "latest";
		?>
	    </span>
	</p>
	<p class="form-field _new_purchase">
	    <?php echo __( 'New purchase', 'woocommerce' ); 
	    woocommerce_wp_text_input( array(
		'id'		=> '_purchase_date',
		'label'	        => __( 'Date' ),
		'desc_tip'	=> 'true',
		'description'	=> __( 'When did you buy this?' ),
		'type' 	        => 'text',
	    ) );
	    
	    woocommerce_wp_text_input( array(
		'id'		=> '_purchase_provider',
		'label'	        => __( 'Provider' ),
		'desc_tip'	=> 'true',
		'description'	=> __( 'Who did you buy this from?' ),
		'type' 	        => 'text',
	    ) );
	    
	    woocommerce_wp_text_input( array(
		'id'		=> '_purchase_qty',
		'label'	        => __( 'Quantity' ),
		'desc_tip'	=> 'true',
		'description'	=> __( 'How much did you buy?' ),
		'type' 	        => 'text',
	    ) );

	    woocommerce_wp_text_input( array(
		'id'		=> '_purchase_price',
		'label'	        => __( 'Price' ),
		'desc_tip'	=> 'true',
		'description'	=> __( 'How much did you pay in total?' ),
		'type' 	        => 'text',
	    ) );

	    //		$myc_save_purchase_nonce = wp_create_nonce("save-purchase");
	    //		$link  = admin_url("admin-ajax.php?action=save_purchase&post_id={$post->ID}&nonce=$myc_save_purchase_nonce");
	    //		echo '<a class="save-purchase-btn" data-save_purchase_nonce="' . $myc_save_purchase_nonce . '" data-post_id="' . $post->ID . '" href="' . $link . '">' . __( 'Save purchase' ) . '</a>';
	    //  echo '<a href="#ajaxthing" class="save-purchase-btn" data-post_id="' . $post->ID . '">' . __( 'Save purchase' ) . '</a>';
	    //		echo '<button class="woocommerce-Button button" name="save-purchase-btn" value="' . esc_attr( $product->get_id() ) . '">' . __( 'Save purchase' ) . '</button>';

	    ?>
	</p>
    </div>
</div>
<?php
}


add_action( 'admin_footer', 'save_ingredient_js' );
function save_ingredient_js() {?>
    <script type="text/javascript">
     jQuery(document).ready(function() {
	 jQuery.find('wp-content-media-buttons').hide();

	 //	 $('.save-purchase-btn').click(function() {
	 var data = {
	     /* '_purchase_date'     : jQuery.closest('div').find('_purchase_date').val(),
		'_purchase_provider' : jQuery.closest('div').find('_purchase_provider').val(),
		'_purchase_qty'      : jQuery.closest('div').find('_purchase_qty').val(),
		'_purchase_price'    : jQuery.closest('div').find('_purchase_price').val(),*/
	     'action': 'save_purchase',
	     'dummy': 123
	 };
	 alert('sending ' + data);
	 jQuery.post(ajaxurl, data, function(response) {
	     alert('Got this from the server: ' + response);
	 });
	 //	 });
     }
    </script>
<?php
}
add_action( 'wp_ajax_save_purchase', 'process_priv_purchase' ); 
function process_priv_purchase () {
    error_log("priv got " . var_export($_POST, true));
    wp_die();
}

add_action( 'wp_ajax_nopriv_save_purchase', 'process_nopriv_purchase' ); 
function process_nopriv_purchase () {
    error_log("nopriv got " . var_export($_POST, true));
    wp_die();
}



function save_ingredient_fields( $post_id ) {
    //    if ( ! wp_verify_nonce( $_REQUEST['myc_save_purchase_nonce'], 'myc_save_purchase_nonce' ) ) {
    //	wp_send_json_error( 'bad_nonce' );
    //	wp_die();
    //    }

    echo "got " . var_export($_POST, true);
    wp_die();
    /*
       if ( !isset($_POST['_purchase_date']) ||
       !isset($_POST['_purchase_provider']) || 
       !isset($_POST['_purchase_price']) || 
       !isset($_POST['_purchase_qty']) ) {
       return;
       }
       $purchase_date     = $_POST['_purchase_date'];
       $purchase_provider = $_POST['_purchase_provider'];
       $purchase_price    = $_POST['_purchase_price'];
       $purchase_qty      = $_POST['_purchase_qty'];
     */
    /*
       add_post_meta( $post_id, '_was_purchased', array( 'date'     => $purchase_date,
       'provider' => $purchase_provider,
       'price'    => $purchase_price,
       'qty'      => $purchase_qty ) );
     */
    /*
       add_post_meta( $post_id, '_prices', array( esc_attr( $_POST['_field_date'] ),
       esc_attr( $_POST['_field_price'] ),
       esc_attr( $_POST['_field_qty'] ),
       esc_attr( (float) $_POST['_field_price'] / (float) $_POST['_field_qty']) ) );
     */
    error_log("save_ingredient_fields");
}
//add_action( 'woocommerce_process_product_meta_ingredient', 'save_ingredient_fields' );
//add_action( 'wp_ajax_save_purchase', 'save_ingredient_fields' );

/**
 * Save the custom fields.

   function save_cooking_option_field( $post_id ) {
   $cooking_option = isset( $_POST['_enable_cooking_option'] ) ? 'yes' : 'no';
   update_post_meta( $post_id, '_enable_cooking_option', $cooking_option );
   if ( isset( $_POST['_text_input_y'] ) ) :
   update_post_meta( $post_id, '_text_input_y', sanitize_text_field( $_POST['_text_input_y'] ) );
   endif;
   }
   add_action( 'woocommerce_process_product_meta_ingredient', 'save_cooking_option_field'  );
   add_action( 'woocommerce_process_product_meta_recipe', 'save_cooking_option_field'  );
 */
