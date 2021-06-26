<?php
/** PRODUCT-OBJECT ******************************************************************/
if(!function_exists('product_object_image')) {
	function product_object_image( $val ) {
        $image_type = apply_filters('product_object_image_type', 'source');
        scmc_template( 'object/image', array('val' => $val, 'image_type' => $image_type ) );
	}
}

if(!function_exists('product_object_title')) {
	function product_object_title( $val ) {
        scmc_template( 'object/title', array('val' => $val ) );
	}
}

if(!function_exists( 'product_object_price' ) ) {
	function product_object_price( $val ) {
        scmc_template( 'object/price', array('val' => $val ) );
	}
}

if(!function_exists('product_object_description' ) ) {
	function product_object_description( $val ) {
        scmc_template( 'object/description', array('val' => $val ) );
	}
}

if(!function_exists('product_object')) {
	function product_object() {
		$product_hiden_title      	= option::get('product_hiden_title');
		$product_hiden_price      	= option::get('product_hiden_price');
		$product_hiden_description  = option::get('product_hiden_description');
		add_action( 'product_object_image',	'product_object_image', 10, 1);
		if( $product_hiden_title ) 			add_action( 'product_object_info',	'product_object_title', 10, 1 );
		if( $product_hiden_price ) 			add_action( 'product_object_info',	'product_object_price', 20, 1 );
		if( $product_hiden_description ) 	add_action( 'product_object_info',	'product_object_description', 30, 1 );
	}
	add_action( 'init','product_object', 10 );
}

if (!function_exists( 'product_object_style' ) ) {
	function product_object_style() {
        scmc_template( 'object/css');
	}
	add_action( 'theme_custom_css_no_tag','product_object_style', 50 );
}