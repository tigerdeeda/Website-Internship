<?php

class WCMp_Stripe_Gateway_Ajax {

    public function __construct() {
        // Ajax Functions
        add_action('wp_ajax_marketplace_stripe_authorize', array(&$this, 'marketplace_stripe_authorize'));
    }
    
    public function marketplace_stripe_authorize(){
        $stripe_settings = get_option('woocommerce_stripe_settings');
        if (isset($stripe_settings) && !empty($stripe_settings)) {
            if (isset($stripe_settings['enabled']) && $stripe_settings['enabled'] == 'no') {
                return;
            }
            $testmode = $stripe_settings['testmode'] === "yes" ? true : false;
            $client_id = $testmode ? get_wcmp_stripe_gateway_settings('test_client_id', 'payment', 'stripe_gateway') : get_wcmp_stripe_gateway_settings('live_client_id', 'payment', 'stripe_gateway');
            $secret_key = $testmode ? $stripe_settings['test_secret_key'] : $stripe_settings['secret_key'];
            if (isset($client_id) && isset($secret_key)) {
                if (isset($_REQUEST['code'])) {
                    $code = $_REQUEST['code'];
                    if (!is_user_logged_in()) {
                        if (isset($_REQUEST['state'])) {
                            $user_id = $_REQUEST['state'];
                        }
                    }else{
                        $user_id = get_current_user_id();
                    }
                    $token_request_body = array(
                        'grant_type' => 'authorization_code',
                        'client_id' => $client_id,
                        'code' => $code,
                        'client_secret' => $secret_key
                    );
                    $req = curl_init('https://connect.stripe.com/oauth/token');
                    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($req, CURLOPT_POST, true);
                    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                    curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($req, CURLOPT_VERBOSE, true);
                    // TODO: Additional error handling
                    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
                    $resp = json_decode(curl_exec($req), true);
                    curl_close($resp);

                    if (!isset($resp['error'])) {
                        update_user_meta($user_id, 'vendor_connected', 1);
                        update_user_meta($user_id, 'admin_client_id', $client_id);
                        update_user_meta($user_id, 'access_token', $resp['access_token']);
                        update_user_meta($user_id, 'refresh_token', $resp['refresh_token']);
                        update_user_meta($user_id, 'stripe_publishable_key', $resp['stripe_publishable_key']);
                        update_user_meta($user_id, 'stripe_user_id', $resp['stripe_user_id']);
                        update_user_meta($user_id, '_vendor_payment_mode', 'stripe_masspay');
                        wp_redirect(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_billing_endpoint', 'vendor', 'general', 'vendor-billing' )));
                        exit();
                    }else{
                        update_user_meta($user_id, 'vendor_connected', 0);
                        wp_redirect(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_billing_endpoint', 'vendor', 'general', 'vendor-billing' )));
                        exit();
                    }
                }
            }
        }
    }

}
