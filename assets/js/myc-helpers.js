/*
jQuery(document).ready(function($){
    $('#datepicker').datepicker();	

});
*/
/*
jQuery(document).ready(function() {
    function show_and_hide_panels() {
	alert('here');
	var product_type    = $( 'select#product-type' ).val();
	var is_virtual      = $( 'input#_virtual:checked' ).length;
	var is_downloadable = $( 'input#_downloadable:checked' ).length;

	// Hide/Show all with rules.
	var hide_classes = '.hide_if_downloadable, .hide_if_virtual';
	var show_classes = '.show_if_downloadable, .show_if_virtual';

	$.each( woocommerce_admin_meta_boxes.product_types, function( index, value ) {
	    hide_classes = hide_classes + ', .hide_if_' + value;
	    show_classes = show_classes + ', .show_if_' + value;
	});

	$( hide_classes ).show();
	$( show_classes ).hide();

	// Shows rules.
	if ( is_downloadable ) {
	    $( '.show_if_downloadable' ).show();
	}
	if ( is_virtual ) {
	    $( '.show_if_virtual' ).show();
	}

        $( '.show_if_' + product_type ).show();

	// Hide rules.
	if ( is_downloadable ) {
	    $( '.hide_if_downloadable' ).hide();
	}
	if ( is_virtual ) {
	    $( '.hide_if_virtual' ).hide();
	}

	$( '.hide_if_' + product_type ).hide();

	$( 'input#_manage_stock' ).change();

	// Hide empty panels/tabs after display.
	$( '.woocommerce_options_panel' ).each( function() {
	    var $children = $( this ).children( '.options_group' );

	    if ( 0 === $children.length ) {
		return;
	    }

	    var $invisble = $children.filter( function() {
		return 'none' === $( this ).css( 'display' );
	    });

	    // Hide panel.
	    if ( $invisble.length === $children.length ) {
		var $id = $( this ).prop( 'id' );
		$( '.product_data_tabs' ).find( 'li a[href="#' + $id + '"]' ).parent().hide();
	    }
	});
    }
    show_and_hide_panels();
});
		    
*/
