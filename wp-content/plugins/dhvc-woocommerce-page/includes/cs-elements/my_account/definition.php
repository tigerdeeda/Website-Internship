<?php

class DHVC_Woo_Product_Page_My_Account extends DHVC_Woo_Page_Cornerstone_Element_Base {

	public function data() {
		return array( 
			'name' => 'dhvc_woo_product_page_my_account', 
			'title' => __( 'WC My Account', DHVC_WOO_PAGE ), 
			'section' => 'WC Single Product', 
			'icon_group' => 'wc-single-product',
			'supports' => array( 'class' ) );
	}

	public function render( $atts ) {
		extract( $atts );
		$shortcode = "[dhvc_woo_product_page_my_account{$extra}]";
		
		return $shortcode;
	}
}