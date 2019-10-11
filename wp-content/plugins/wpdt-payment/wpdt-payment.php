<?php
/*
Plugin Name: WPDT Payment 
Plugin URI: http://wpdevthai.com
Description: ระบบแจ้งการชำระเงินไทย
Version: 1.0
Author: Wisan Namwongsa
Author http://wisan.me/
License: A "Slug" license name e.g. GPL2
*/

$wpdt_payment_banks_info['BKKB'] = array("name"=>'กรุงเทพ', 		"icon"=>plugins_url( '/images/BKKB.png',__FILE__ ));
$wpdt_payment_banks_info['SICO'] = array("name"=>'ไทยพาณิชย์', 	"icon"=>plugins_url( '/images/SICO.png',__FILE__ ));
$wpdt_payment_banks_info['KASI'] = array("name"=>'กสิกรไทย', 	"icon"=>plugins_url( '/images/KASI.png',__FILE__ ));
$wpdt_payment_banks_info['KRTH'] = array("name"=>'กรุงไทย', 		"icon"=>plugins_url( '/images/KRTH.png',__FILE__ ));
$wpdt_payment_banks_info['AYUD'] = array("name"=>'กรุงศรีอยุธยา', 	"icon"=>plugins_url( '/images/AYUD.png',__FILE__ ));
$wpdt_payment_banks_info['TMBK'] = array("name"=>'ทหารไทย', 		"icon"=>plugins_url( '/images/TMBK.jpg',__FILE__ ));
$wpdt_payment_banks_info['GSBA'] = array("name"=>'ออมสิน', 		"icon"=>plugins_url( '/images/GSBA.png',__FILE__ ));
$wpdt_payment_banks_info['THBK'] = array("name"=>'ธนชาต', 		"icon"=>plugins_url( '/images/THBK.png',__FILE__ ));
$wpdt_payment_banks_info['BKAS'] = array("name"=>'ยูโอบี', 		"icon"=>plugins_url( '/images/BKAS.png',__FILE__ ));
$wpdt_payment_banks_info['PromptPay'] = array("name"=>'พร้อมเพย์', "icon"=>plugins_url( '/images/PromptPay.png',__FILE__ ));

register_activation_hook( __FILE__, 'wpdt_pament_plugin_activate' );

function wpdt_pament_plugin_activate(){
	$wpdt_pament_options	= array(
		'wpdt_payment_email_to' => '',
		'wpdt_payment_email_form' => '',
		'wpdt_payment_email_subject' => '',
		'wpdt_payment_types' => '',
		'wpdt_payment_bank_accounts' =>array(),
		'wpdt_payment_form_text_success'=>'<p>เราได้รับข้อมูลการแจ้งชำระเงินจากท่านแล้ว ท่านจะได้รับ Email เปิดบริการภายใน 24 ชั่วโมง <br/>ขอบพระคุณที่ท่านไว้วางใจเลือกใช้บริการจากเรา</p>'
	);
	
	add_option( 'wpdt_pament_options',$wpdt_pament_options);
}



add_action('admin_menu', 'register_wpdt_pament_submenu_page');

function register_wpdt_pament_submenu_page() {
	add_submenu_page( 'options-general.php', 'WPDT Payment', 'WPDT Payment', 'manage_options', 'wpdt-pament-setting-page', 'wpdt_payment_setting_page_callback' ); 
}

function wpdt_payment_setting_page_callback() {

	$nonce= @$_REQUEST['wpdt_payment_wpnonce'];
	if (wp_verify_nonce($nonce, 'wpdt_payment_wpnonce') ){

		global $wpdt_payment_banks_info;

		$wpdt_payment_bank_accounts =array();

		foreach ($_POST['wpdt_bank'] as $key=>$value) {
 				$wpdt_payment_bank_accounts["wpdt_banks"][$key] 				= $value;//echo $_POST['wpdt_bank'][]
 				$wpdt_payment_bank_accounts["wpdt_banks_icon"][$key] 			= $wpdt_payment_banks_info[$value]['icon'];
 				$wpdt_payment_bank_accounts["wpdt_banks_name"][$key] 			= $wpdt_payment_banks_info[$value]['name'];
 				$wpdt_payment_bank_accounts["wpdt_banks_branch"][$key] 			= $_POST['wpdt_bank_branch'][$key];
 				$wpdt_payment_bank_accounts["wpdt_banks_account_name"][$key] 	= $_POST['wpdt_bank_account_name'][$key];
 				$wpdt_payment_bank_accounts["wpdt_banks_account_number"][$key] 	= $_POST['wpdt_bank_account_number'][$key];		

 			}

 			$wpdt_pament_options	= array(
 				'wpdt_payment_email_to' 		=>  $_POST['wpdt_payment_email_to'],
 				'wpdt_payment_email_form' 		=>  $_POST['wpdt_payment_email_form'],
 				'wpdt_payment_email_subject' 	=>  $_POST['wpdt_payment_email_subject'],
								'wpdt_payment_types' 			=>  $_POST['wpdt_payment_types'], //$wpdt_payment_types,
								'wpdt_payment_bank_accounts' 	=>  $wpdt_payment_bank_accounts,
								'wpdt_payment_form_text_success' => $_POST['wpdt_payment_form_text_success']
							);
 			update_option( 'wpdt_pament_options',$wpdt_pament_options);	
 		}
 		include('views/form-setting.php');
 	}


 	add_action('wp_enqueue_scripts', 'wpdt_payment_enqueue_script');

 	function wpdt_payment_enqueue_script() {
 		wp_enqueue_script('validate', plugins_url('js/jquery.validate.js', __FILE__), array('jquery'), true);

 		wp_register_style('wpdt-payment-style',  plugins_url('wpdt-payment.css', __FILE__));
 		wp_enqueue_style( 'wpdt-payment-style');
 	}

 	function wpdt_pamyent(){ echo wpdt_payment_form();}	


 	function wpdt_pamyent_upload_dir($upload) {

 		$upload['subdir'] = '/payment' . $upload['subdir'];

 		$upload['path']   = $upload['basedir'] . $upload['subdir'];

 		$upload['url']    = $upload['baseurl'] . $upload['subdir'];

 		return $upload;

 	}


 	add_shortcode( 'WPDTPAYMENT', 'wpdt_payment_form' );

 	function wpdt_payment_form(){

 		ob_start();

 		$wpdt_pament_options = get_option('wpdt_pament_options');
 		$wpdt_payment_bank_accounts = $wpdt_pament_options['wpdt_payment_bank_accounts'];

 		$nonce= @$_REQUEST['wpdt_payment_form_wpnonce'];

 		if (wp_verify_nonce($nonce, 'wpdt_payment_form_wpnonce') ){

 			$message =  'ประเภทรายการที่ชำระ : '.$_POST['wpdt_payment_form_p_type']."\n";

 			$message.= 'ชื่อผู้โอน : '.$_POST['wpdt_payment_form_c_name']."\n";
 			$message.= 'เบอร์โทร : '.$_POST['wpdt_payment_form_c_tel']."\n";
 			$message.= 'อีเมล์ : '.$_POST['wpdt_payment_form_c_email']."\n";
 			$message.= 'เลขที่อ้างอิง : '.$_POST['wpdt_payment_form_rf_id']."\n";

 			$message.= 'วันที่โอนเงิน : '.$_POST['wpdt_payment_form_t_day'].'/'.$_POST['wpdt_payment_form_t_mount'].'/'.$_POST['wpdt_payment_form_t_year'].' '.$_POST['wpdt_payment_formt_time']."\n";
 			$message.= 'จำนวนเงินที่โอน : '.$_POST['wpdt_payment_formtotal']."\n";
 			$message.= 'ธนาคารที่โอน : '.$_POST['bank_select']."\n";
 			$message.= 'รายละเอียดเพิ่มเติม : '.$_POST['wpdt_payment_formother_detail']."\n"; 

 			/*******************************************************************************/

 			$to 		= $wpdt_pament_options['wpdt_payment_email_to'];
 			$subject 	= $wpdt_pament_options['wpdt_payment_email_subject']." - ".$_POST['wpdt_payment_form_p_type']."  #".$_POST['wpdt_payment_form_rf_id'];
 			
 			$from		= $wpdt_pament_options['wpdt_payment_email_form']; //$_POST['wpdt_payment_form_c_email']; //
 			$from_name	= $_POST['wpdt_payment_form_c_name'];
 			$headers 	= "From:" . $from;


 			$attachments = false;

 			if ( ! function_exists( 'wp_handle_upload' ) ) 
 				require_once( ABSPATH . 'wp-admin/includes/file.php' );
 			$uploadedfile = $_FILES['wpdt_payment_upload_file'];
 			$upload_overrides = array( 'test_form' => false );
 			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
 			remove_filter('upload_dir', 'wpdt_pamyent_upload_dir');

 			if ( $movefile ) {
 				$attachments = $movefile['file'];
 			} else {
 				$attachments = false;
 			}

 			$headers = 'From: '.$from_name.' <'.$from.'>' . "\r\n";

 			wp_mail( $to,$subject, $message, $headers, $attachments );


 			/********************************************************************************/

 			do_action('wpdt_payment_submit_success');

 			/********************************************************************************/

 			echo '<div class="wpde-payment-success alert alert-success">';
 			echo $wpdt_pament_options['wpdt_payment_form_text_success'];
 			echo '</div>';

 		}else{
 			include('views/form-pament.php');
 		}
 		$out1 = ob_get_contents();
 		ob_end_clean();
 		return $out1;
 	}


 	add_shortcode( 'WPDT_PAYMENT_BANKS', 'wpdt_payment_banks' );

 	function wpdt_payment_banks(){

 		ob_start();

 		$wpdt_pament_options = get_option('wpdt_pament_options');
 		$wpdt_payment_bank_accounts = $wpdt_pament_options['wpdt_payment_bank_accounts'];


 		?>

 		<div class="cart-collaterals">

 			<table class="wpdt-payment-table-banks woocommerce-table  shop_table ">

 				<thead>
 					<tr>
 						<th></th>
 						<th>ธนาคาร</th>
 						<th>ชื่อบัญชี </th>
 						<th><span class="wpdt_payment_form_account_number_text">เลขที่บัญชี</span></th>
 					</tr>
 				</thead>
 				<tbody>

 					<?php

 					if(count($wpdt_payment_bank_accounts['wpdt_banks']) > 0 ){
 						foreach ($wpdt_payment_bank_accounts['wpdt_banks'] as $key => $value) {

 							if($value){
 								?>
 								<tr>
 									<td class="thead text-center">
 										<img src="<?php echo $wpdt_payment_bank_accounts['wpdt_banks_icon'][$key]; ?>" height="30" width="30" align="middle" alt="<?php echo $wpdt_payment_bank_accounts['wpdt_banks_name'][$key]; ?>">
 									</td>
 									<td>
 										<div class="wpdt_payment_form_bank_info">
 											<span class="wpdt_payment_form_bank_name" style="color: #0066FF;"><?php echo $wpdt_payment_bank_accounts['wpdt_banks_name'][$key]; ?></span> 
 											<span class="wpdt_payment_form_bank_branch"><?php echo $wpdt_payment_bank_accounts['wpdt_banks_branch'][$key]; ?></span>
 										</div>
 									</td>
 									<td><span class="wpdt_payment_form_account_name" style="color: green;" ><?php echo $wpdt_payment_bank_accounts['wpdt_banks_account_name'][$key]; ?></span></td>
 									<td> <span class="wpdt_payment_form_account_number" style="color: red;"><?php echo $wpdt_payment_bank_accounts['wpdt_banks_account_number'][$key]; ?></span></td>

 								</tr>
 								<?php 

 							}

 						} 
 					} 

 					?>    
 				</tbody>
 			</table>
 		</div>

 		<?php

 		$out1 = ob_get_contents();
 		ob_end_clean();
 		return $out1;

 	}



 	add_action( 'woocommerce_email_before_order_table', 'wpdt_payment_email_instructions', 10, 3 );

 	/**
	 * HOOK instructions SHORTCODE in WC emails.
	 *
	 * @param WC_Order $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 */
 	function wpdt_payment_email_instructions( $order, $sent_to_admin, $plain_text = false ) {

	// Get the gateway object
 		$gateways           = WC_Payment_Gateways::instance();
 		$available_gateways = $gateways->get_available_payment_gateways();
 		$gateway            = isset( $available_gateways['bacs'] ) ? $available_gateways['bacs'] : false;

	// We won't do anything if the gateway is not available
 		if ( false == $gateway ) {
 			return;
 		}

 		if ( ! $sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
 			if ( $gateway->instructions ) {
 				echo wpautop( wptexturize( do_shortcode( $gateway->instructions ) ) ) . PHP_EOL;
 			}
 			//$gateway->bank_details( $order->get_id() );
 		}

 	}



 	add_action( 'woocommerce_thankyou_bacs', 'wpdt_payment_woocommerce_thankyou_bacs' );

 	/**
	 * HOOK instructions SHORTCODE in WC emails.
	 *
	 * @param WC_Order $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 */
 	function wpdt_payment_woocommerce_thankyou_bacs( $order ) {

		// Get the gateway object
 		$gateways           = WC_Payment_Gateways::instance();
 		$available_gateways = $gateways->get_available_payment_gateways();
 		$gateway            = isset( $available_gateways['bacs'] ) ? $available_gateways['bacs'] : false;

		// We won't do anything if the gateway is not available
 		if ( false == $gateway ) {
 			return;
 		}

 		if ( $gateway->instructions ) {
 			echo wpautop( wptexturize( do_shortcode( $gateway->instructions ) ) ) . PHP_EOL;
 		}

 	}


	// Remove the original bank details
 	add_action( 'init', 'wpdt_payment_prefix_remove_bank_details', 100 );
 	function wpdt_payment_prefix_remove_bank_details() {

		// Do nothing, if WC_Payment_Gateways does not exist
 		if ( ! class_exists( 'WC_Payment_Gateways' ) ) {
 			return;
 		}

		// Get the gateways instance
 		$gateways           = WC_Payment_Gateways::instance();

		// Get all available gateways, [id] => Object
 		$available_gateways = $gateways->get_available_payment_gateways();

 		if ( isset( $available_gateways['bacs'] ) ) {
		// If the gateway is available, remove the action hook
 			remove_action( 'woocommerce_email_before_order_table', array( $available_gateways['bacs'], 'email_instructions' ), 10, 3 );
 			
 		}
 	}


 	add_action( 'init', 'wpdt_payment_remove_bacs_from_thank_you_page', 100 );
 	function wpdt_payment_remove_bacs_from_thank_you_page() {

		// Bail, if we don't have WC function
 		if ( ! function_exists( 'WC' ) ) {
 			return;
 		}

		// Get all available gateways
 		$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

		// Get the Bacs gateway class
 		$gateway = isset( $available_gateways['bacs'] ) ? $available_gateways['bacs'] : false;

		// We won't do anything if the gateway is not available
 		if ( false == $gateway ) {
 			return;
 		}

		// Remove the action, which places the BACS details on the thank you page
 		remove_action( 'woocommerce_thankyou_bacs', array( $gateway, 'thankyou_page' ) );
 	}


 	?>
