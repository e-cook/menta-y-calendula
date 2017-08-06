<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function create_meal_store() {
    /**
     * WC Meal Product Data Store: Stored in CPT.
     *
     * @version  0.1
     * @category Class
     * @author   MYC
     */
    class WC_Product_Meal_Data_Store_CPT extends WC_Product_Variable_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Variable_Data_Store_Interface {
	
    }
}
add_action( 'plugins_loaded', 'create_meal_store' );
