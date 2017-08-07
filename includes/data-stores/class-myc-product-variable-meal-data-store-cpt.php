<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('plugins_loaded', function () {
    /**
     * WC Meal Product Data Store: Stored in CPT.
     *
     * @version  0.1
     * @category Class
     * @author   MYC
     */
    class WC_Product_Variable_Meal_Data_Store_CPT extends WC_Product_Variable_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Variable_Data_Store_Interface {
	
    }
});
