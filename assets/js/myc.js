/* Registration Ajax */
jQuery('#submit-purchase').on('click',function(){
    
    var action = 'register_action';
    
    var purchase_date     = jQuery("#_purchase_date").val();
    var purchase_provider = jQuery("#_purchase_provider").val();
    var purchase_qty      = jQuery("#_purchase_qty").val();
    var purchase_price    = jQuery("#_purchase_price").val();
    
    var ajaxdata = {
	
	action: 'register_action',
	username: username,
	mail_id: mail_id,
	firname: firname,
	lasname: lasname,
	passwrd: passwrd,
	
    };
    
    jQuery.post( ajaxurl, ajaxdata, function(res){ // ajaxurl must be defined previously
	
	jQuery("#error-message").html(res);
    });
});
