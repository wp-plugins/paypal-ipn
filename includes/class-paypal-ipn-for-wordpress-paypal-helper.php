<?php

/**
 * PayPal IPN helper class
 *
 * This class defines all code necessary to Paypal IPN Helper Function
 *
 * @since      1.0.0
 * @package    paypal-ipn-for-wordpress
 * @subpackage paypal-ipn-for-wordpress/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Paypal_Helper {

    /**
     * Constructor for the Paypal_Helper.
     */
    public function __construct() {

        $this->debug = (get_option('paypal_ipn_for_wordpress_paypal_debug') == '1') ? 'yes' : 'no';
        $this->liveurl = 'https://www.paypal.com/cgi-bin/webscr';
        $this->testurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

        // Logs
        if ('yes' == $this->debug) {
            $this->log = new AngellEYE_Paypal_Ipn_For_Wordpress_Logger();
        }
    }

    /**
     * check_ipn_request helper function use for check ipn request is valid or not
     * @since    1.0.0
     * @access   public
     * return boolean
     */
    public function check_ipn_request() {
        /**
         * Check for PayPal IPN Response
         */
        @ob_clean();

        $ipn_response = !empty($_POST) ? $_POST : false;

        $return = $this->paypal_ipn_for_wordpress_check_adaptive_paments_is_vlidate($ipn_response);

        if (isset($return['validate']) && ($return['validate'] == 'required_to_check')) {

            // If $_POST is empty return without process
            if ($ipn_response == false) {
                return false;
            }

            if ($ipn_response && $this->check_ipn_request_is_valid($ipn_response)) {

                header('HTTP/1.1 200 OK');

                do_action("paypal_ipn_for_wordpress_valid_ipn_request", $ipn_response);

                return true;
            } else {

                do_action("paypal_ipn_for_wordpress_ipn_request_failed", "PayPal IPN Request Failure", array('response' => 200));

                return false;
            }
        }

        return $return;
    }

    /**
     * check_ipn_request_is_valid helper function use when IPN response is valid
     * @since    1.0.0
     * return boolean
     */
    public function check_ipn_request_is_valid($ipn_response) {

        /**
         *  paypal_ipn_for_wordpress_ipn_forwarding_handler action allow developer to trigger own function
         */
        do_action('paypal_ipn_for_wordpress_ipn_forwarding_handler', $ipn_response);

        /**
         * allow developer paypal_ipn_for_wordpress_ipn_response_handler to trigger own function
         */
        do_action('paypal_ipn_for_wordpress_ipn_response_handler', $ipn_response);

        if ('yes' == $this->debug) {
            $this->log->add('paypal', 'IPN paypal_ipn_for_wordpress_ipn_forwarding_handler: ' . print_r($ipn_response, true));
        }

        $is_sandbox = (isset($ipn_response['test_ipn'])) ? 'yes' : 'no';

        if ('yes' == $is_sandbox) {
            $paypal_adr = $this->testurl;
        } else {
            $paypal_adr = $this->liveurl;
        }

        if ('yes' == $this->debug) {
            $this->log->add('paypal', 'Checking IPN response is valid via ' . $paypal_adr . '...');
        }

        // Get received values from post data
        $validate_ipn = array('cmd' => '_notify-validate');
        $validate_ipn += stripslashes_deep($ipn_response);

        // Send back post vars to paypal
        $params = array(
            'body' => $validate_ipn,
            'sslverify' => false,
            'timeout' => 60,
            'httpversion' => '1.0.0',
            'compress' => false,
            'decompress' => false,
            'user-agent' => 'paypal-ipn/'
        );

        if ('yes' == $this->debug) {
            $this->log->add('paypal', 'IPN Request: ' . print_r($params, true));
        }

        // Post back to get a response
        $response = wp_remote_post($paypal_adr, $params);

        if ('yes' == $this->debug) {
            $this->log->add('paypal', 'IPN Response: ' . print_r($response, true));
        }

        // check to see if the request was valid
        if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
            if ('yes' == $this->debug) {
                $this->log->add('paypal', 'Received valid response from PayPal');
            }

            return true;
        }

        if ('yes' == $this->debug) {
            $this->log->add('paypal', 'Received invalid response from PayPal');
            if (is_wp_error($response)) {
                $this->log->add('paypal', 'Error response: ' . $response->get_error_message());
            }
        }

        return false;
    }

    /**
     * successful_request helper function use for parse data 
     * @since    1.0.0
     * @param array $posted
     * return boolean
     */
    public function successful_request($IPN_status) {

        $ipn_response = !empty($_POST) ? $_POST : false;

        $this->log->add('paypal', 'Payment IPN Array: ' . print_r($ipn_response, true));
        // If $_POST is empty return without process
        if ($ipn_response == false) {
            return false;
        }

        $ipn_response['IPN_status'] = ( $IPN_status == true ) ? 'Verified' : 'Invalid';

        if ('yes' == $this->debug) {
            $this->log->add('paypal', 'Payment IPN_status: ' . $IPN_status);
        }

        $txn_type = (isset($ipn_response['txn_type'])) ? $ipn_response['txn_type'] : '';
        $reason_code = (isset($ipn_response['reason_code'])) ? $ipn_response['reason_code'] : '';
        $payment_status = (isset($ipn_response['payment_status'])) ? $ipn_response['payment_status'] : '';
        $account_key = (isset($ipn_response['account_key'])) ? $ipn_response['account_key'] : '';
        $transaction_type = (isset($ipn_response['transaction_type'])) ? $ipn_response['transaction_type'] : '';

        if (strtoupper($transaction_type) == 'ADAPTIVE PAYMENT PREAPPROVAL' || strtoupper($transaction_type) == 'ADAPTIVE PAYMENT PAY' || !empty($account_key)) {
            $posted = $this->decodePayPalIPN();
            $posted['IPN_status'] = $ipn_response['IPN_status'];
            $posted = stripslashes_deep($posted);
        } else {
            $posted = stripslashes_deep($ipn_response);
        }

        if (isset($posted['txn_type']) && $posted['txn_type'] == 'masspay') {

            $i = 1;
            $postedmasspay = array();
            while (isset($posted['masspay_txn_id_' . $i])) {
                $masspay_txn_id = isset($posted['masspay_txn_id_' . $i]) ? $posted['masspay_txn_id_' . $i] : '';
                $mc_currency = isset($posted['mc_currency_' . $i]) ? $posted['mc_currency_' . $i] : '';
                $mc_fee = isset($posted['mc_fee_' . $i]) ? $posted['mc_fee_' . $i] : 0;
                $mc_gross = isset($posted['mc_gross_' . $i]) ? $posted['mc_gross_' . $i] : 0;
                $receiver_email = isset($posted['receiver_email_' . $i]) ? $posted['receiver_email_' . $i] : '';
                $status = isset($posted['status_' . $i]) ? $posted['status_' . $i] : '';
                $unique_id = isset($posted['unique_id_' . $i]) ? $posted['unique_id_' . $i] : '';

                $postedmasspay = array(
                    'masspay_txn_id' => $masspay_txn_id,
                    'mc_currency' => $mc_currency,
                    'mc_fee' => $mc_fee,
                    'mc_gross' => $mc_gross,
                    'receiver_email' => $receiver_email,
                    'status' => $status,
                    'unique_id' => $unique_id,
                    'payment_date' => $posted['payment_date'],
                    'payment_status' => $posted['payment_status'],
                    'charset' => $posted['charset'],
                    'first_name' => $posted['first_name'],
                    'notify_version' => $posted['notify_version'],
                    'payer_status' => $posted['payer_status'],
                    'verify_sign' => $posted['verify_sign'],
                    'payer_email' => $posted['payer_email'],
                    'payer_business_name' => $posted['payer_business_name'],
                    'last_name' => $posted['last_name'],
                    'txn_type' => $posted['txn_type'],
                    'residence_country' => $posted['residence_country'],
                    'ipn_track_id' => $posted['ipn_track_id'],
                    'IPN_status' => $ipn_response['IPN_status']
                );

                $this->successfull_request_handler($postedmasspay);
                $this->ipn_response_data_handler($postedmasspay);

                $i++;
            }
        } else {

            $i = 1;
            $cart_items = array();
            while (isset($posted['item_number' . $i])) {
                $item_number = isset($posted['item_number' . $i]) ? $posted['item_number' . $i] : '';
                $item_name = isset($posted['item_name' . $i]) ? $posted['item_name' . $i] : '';
                $quantity = isset($posted['quantity' . $i]) ? $posted['quantity' . $i] : '';
                $mc_gross = isset($posted['mc_gross_' . $i]) ? $posted['mc_gross_' . $i] : 0;
                $mc_handling = isset($posted['mc_handling' . $i]) ? $posted['mc_handling' . $i] : 0;
                $mc_shipping = isset($posted['mc_shipping' . $i]) ? $posted['mc_shipping' . $i] : 0;
                $custom = isset($posted['custom' . $i]) ? $posted['custom' . $i] : '';
                $option_name1 = isset($posted['option_name1_' . $i]) ? $posted['option_name1_' . $i] : '';
                $option_selection1 = isset($posted['option_selection1_' . $i]) ? $posted['option_selection1_' . $i] : '';
                $option_name2 = isset($posted['option_name2_' . $i]) ? $posted['option_name2_' . $i] : '';
                $option_selection2 = isset($posted['option_selection2_' . $i]) ? $posted['option_selection2_' . $i] : '';
                $btn_id = isset($posted['btn_id' . $i]) ? $posted['btn_id' . $i] : '';
                $tax = isset($posted['tax' . $i]) ? $posted['tax' . $i] : '';

                $current_item = array(
                    'item_number' => $item_number,
                    'item_name' => $item_name,
                    'quantity' => $quantity,
                    'mc_gross' => $mc_gross,
                    'mc_handling' => $mc_handling,
                    'mc_shipping' => $mc_shipping,
                    'custom' => $custom,
                    'option_name1' => $option_name1,
                    'option_selection1' => $option_selection1,
                    'option_name2' => $option_name2,
                    'option_selection2' => $option_selection2,
                    'btn_id' => $btn_id,
                    'tax' => $tax
                );

                array_push($cart_items, $current_item);
                $i++;
            }

            // If cart_items is not emptry
            if (is_array($cart_items) && !empty($cart_items)) {
                $posted['cart_items'] = $cart_items;
            }

            $this->successfull_request_handler($posted);
            $this->ipn_response_data_handler($posted);
        }
    }

    /**
     * successfull_request_handler helper function use when IPN response is Successful
     * @since    1.0.0
     * @param array $posted
     * return boolean
     */
    public function successfull_request_handler($posted = null) {

        if (isset($posted['payment_status']) && !empty($posted['payment_status'])) {

            if ('yes' == $this->debug) {
                $this->log->add('paypal', 'Payment status: ' . $posted['payment_status']);
            }

            /* developers to trigger their own functions based on different payment_status values received by PayPal IPN's.
             * $posted array contain all the response variable from received by PayPal IPN's
             */

            do_action('paypal_ipn_for_wordpress_payment_status_' . strtolower($posted['payment_status']), $posted);
        }

        if (isset($posted['status']) && !empty($posted['status'])) {

            if ('yes' == $this->debug) {
                $this->log->add('paypal', 'Payment status: ' . $posted['status']);
            }

            /* developers to trigger their own functions based on different status values received by PayPal IPN's.
             * $posted array contain all the response variable from received by PayPal IPN's
             */

            do_action('paypal_ipn_for_wordpress_adaptive_status_' . strtolower(str_replace(' ', '_', $posted['status'])), $posted);
        }

        if (isset($posted['txn_type']) && !empty($posted['txn_type'])) {

            if ('yes' == $this->debug) {
                $this->log->add('paypal', 'Payment transaction type: ' . $posted['txn_type']);
            }

            /* developers to trigger their own functions based on different txn_type values received by PayPal IPN's.
             * $posted array contain all the response variable from received by PayPal IPN's
             */

            do_action('paypal_ipn_for_wordpress_txn_type_' . strtolower($posted['txn_type']), $posted);
        }

        if (isset($posted['transaction_type']) && !empty($posted['transaction_type'])) {

            if ('yes' == $this->debug) {
                $this->log->add('paypal', 'Payment transaction type: ' . $posted['transaction_type']);
            }

            /* developers to trigger their own functions based on different transaction_type values received by PayPal IPN's.
             * $posted array contain all the response variable from received by PayPal IPN's
             */

            if($posted['transaction_type'] == 'Adjustment') {
            	do_action('paypal_ipn_for_wordpress_adaptive' . strtolower(str_replace(' ', '_', $posted['transaction_type'])), $posted);
            } else {
            	do_action('paypal_ipn_for_wordpress_' . strtolower(str_replace(' ', '_', $posted['transaction_type'])), $posted);
            }
        }

        /**
         * Store IPN response to post table with ipn_type post type
         */
    }

    /**
     * ipn_response_data_handler helper function use for further process 
     * @since    1.0.0
     * return boolean
     */
    public function ipn_response_data_handler($posted = null) {
        /**
         * Create array for store data to post table.
         */
        global $wp;

        if (isset($posted) && !empty($posted)) {

            /**
             * check payment status is available because some of PayPal transaction payment_status is not available 
             */
            if (isset($posted['payment_status']) && !empty($posted['payment_status'])) {
                $payment_status = ucfirst(str_replace('_', ' ', $posted['payment_status']));

                $term = term_exists($payment_status, 'paypal_ipn_type');

                if ($term !== 0 && $term !== null) {
                    
                } else {

                    $term = wp_insert_term($payment_status, 'paypal_ipn_type', array('slug' => $posted['payment_status']));
                }
            }

            if (isset($posted['txn_id'])) {
                $paypal_txn_id = $posted['txn_id'];
            } elseif (isset($posted['subscr_id'])) {
                $paypal_txn_id = $posted['subscr_id'];
            } elseif (isset($posted['recurring_payment_id'])) {
                $paypal_txn_id = $posted['recurring_payment_id'];
            } elseif (isset($posted['masspay_txn_id'])) {
                $paypal_txn_id = $posted['masspay_txn_id'];
            } elseif (isset($posted['transaction_id'])) {
                $paypal_txn_id = $posted['transaction_id'];
            } elseif (isset($posted['account_key'])) {
                $paypal_txn_id = $posted['account_key'];
            } elseif (isset($posted['preapproval_key'])) {
                $paypal_txn_id = $posted['preapproval_key'];
            } elseif (isset($posted['pay_key'])) {
                $paypal_txn_id = $posted['pay_key'];
            }

            $new_posted = $this->paypal_ipn_for_wordpress_parse_ipn_data($posted);
            //txn_type_own

            /**
             *  development hook paypal_ipn_for_wordpress_mailchimp_handler 
             */
            if ('yes' == get_option('enable_mailchimp')) {
                if (isset($new_posted['txn_type_own']) && !empty($new_posted['txn_type_own'])) {
                    $txn_type_own = ($new_posted['txn_type_own'] == 'recurring_payments_p') ? 'recurring_payment_profile' : $new_posted['txn_type_own'];
                    if ('yes' == get_option($txn_type_own)) {
                        do_action('paypal_ipn_for_wordpress_mailchimp_handler', $posted);
                    }
                }
            }

            if (isset($new_posted['txn_type_own'])) {
                $post_status = $new_posted['txn_type_own'];
            } elseif (isset($posted['txn_type'])) {
                $post_status = $new_posted['txn_type'];
            } elseif (isset($posted['transaction_type'])) {
                $post_status = $new_posted['transaction_type'];
            } else {
                $post_status = 'Not-Available';
            }

            if ($this->paypal_ipn_for_wordpress_exist_post_by_title($paypal_txn_id) == false) {

                $insert_ipn_array = array(
                    'ID' => '',
                    'post_type' => 'paypal_ipn', // Custom Post Type Slug
                    'post_status' => $post_status,
                    'post_title' => $paypal_txn_id,
                );

                $post_id = wp_insert_post($insert_ipn_array);

                /**
                 * check payment status is available because some of PayPal transaction payment_status is not available 
                 */
                if (isset($posted['payment_status']) && !empty($posted['payment_status'])) {
                    $tag[] = $term['term_id'];

                    $update_term = wp_set_post_terms($post_id, $tag, 'paypal_ipn_type');

                    _update_generic_term_count($term['term_taxonomy_id'], 'paypal_ipn_type');
                }

                $this->ipn_response_postmeta_handler($post_id, $posted);
            } else {

                $post_id = $this->paypal_ipn_for_wordpress_exist_post_by_title($paypal_txn_id);

                wp_update_post(array('ID' => $post_id, 'post_status' => $post_status));

                $this->ipn_response_postmeta_handler($post_id, $posted);
            }
        }
    }

    /**
     * ipn_response_postmeta_handler helper function used for store ipn response data to post meta field
     * @since    1.0.0
     * @access   public
     */
    public function ipn_response_postmeta_handler($post_id, $posted) {
        update_post_meta($post_id, 'ipn data serialized', $posted);
        foreach ($posted as $metakey => $metavalue)
            update_post_meta($post_id, $metakey, $metavalue);
    }

    /**
     * paypal_ipn_for_wordpress_exist_post_by_title helper function used for check txn_id as post_title is exist or not
     * @since    1.0.0
     * @access   public
     */
    function paypal_ipn_for_wordpress_exist_post_by_title($ipn_txn_id) {

        global $wpdb;

        $post_data = $wpdb->get_col($wpdb->prepare("SELECT ID FROM wp_posts WHERE post_title = %s AND post_type = %s ", $ipn_txn_id, 'paypal_ipn'));

        if (empty($post_data)) {

            return false;
        } else {

            return $post_data[0];
        }
    }

    /**
     * paypal_ipn_for_wordpress_parse_ipn_data helper function used for make own txn_type 
     * @since    1.0.0
     * @access   public
     */
    public function paypal_ipn_for_wordpress_parse_ipn_data($posted = null) {

        $newposted = array();
        $txn_type = (isset($posted['txn_type'])) ? $posted['txn_type'] : '';
        $reason_code = (isset($posted['reason_code'])) ? $posted['reason_code'] : '';
        $payment_status = (isset($posted['payment_status'])) ? $posted['payment_status'] : '';
        $account_key = (isset($posted['account_key'])) ? $posted['account_key'] : '';
        $transaction_type = (isset($posted['transaction_type'])) ? $posted['transaction_type'] : '';

        if (strtoupper($txn_type) == 'NEW_CASE' || strtoupper($payment_status) == 'REVERSED' || strtoupper($payment_status) == 'CANCELED_REVERSAL' || strtoupper($txn_type) == 'ADJUSTMENT') {

            $newposted['txn_type_own'] = 'disputes';
        } elseif (strtoupper($reason_code) == 'REFUND') {

            $newposted['txn_type_own'] = 'refund';
        } elseif (strtoupper($txn_type) == 'MASSPAY') {

            $newposted['txn_type_own'] = 'mass_payments';
        } elseif (strtoupper($txn_type) == 'MC_CANCEL' || strtoupper($txn_type) == 'MC_SIGNUP') {

            $newposted['txn_type_own'] = 'billing_agreements';
        } elseif (strtoupper($txn_type) == 'PAYOUT') {

            $newposted['txn_type_own'] = 'payouts';
        } elseif (strtoupper($txn_type) == 'SUBSCR_SIGNUP' || strtoupper($txn_type) == 'SUBSCR_FAILED' || strtoupper($txn_type) == 'SUBSCR_CANCEL' || strtoupper($txn_type) == 'SUBSCR_EOT' || strtoupper($txn_type) == 'SUBSCR_MODIFY') {

            $newposted['txn_type_own'] = 'subscriptions';
        } elseif (strtoupper($txn_type) == 'SUBSCR_PAYMENT') {

            $newposted['txn_type_own'] = 'subscription_payment';
        } elseif (strtoupper($txn_type) == 'MERCH_PMT') {

            $newposted['txn_type_own'] = 'merchant_payments';
        } elseif (strtoupper($txn_type) == 'MP_CANCEL' || strtoupper($txn_type) == 'MP_SIGNUP') {

            $newposted['txn_type_own'] = 'billing_agreements';
        } elseif (strtoupper($txn_type) == 'RECURRING_PAYMENT_PROFILE_CREATED' || strtoupper($txn_type) == 'RECURRING_PAYMENT_PROFILE_CANCEL' || strtoupper($txn_type) == 'RECURRING_PAYMENT_PROFILE_MODIFY') {

            $newposted['txn_type_own'] = 'recurring_payments_p';
        } elseif (strtoupper($txn_type) == 'RECURRING_PAYMENT' || strtoupper($txn_type) == 'RECURRING_PAYMENT_SKIPPED' || strtoupper($txn_type) == 'RECURRING_PAYMENT_FAILED' || strtoupper($txn_type) == 'RECURRING_PAYMENT_SUSPENDED_DUE_TO_MAX_FAILED_PAYMENT' || strtoupper($txn_type) == 'RECURRING_PAYMENT_EXPIRED' || strtoupper($txn_type) == 'RECURRING_PAYMENT_SUSPENDED') {

            $newposted['txn_type_own'] = 'recurring_payments';
        } elseif (strtoupper($reason_code) != 'REFUND' && ( strtoupper($txn_type) == 'CART' || strtoupper($txn_type) == 'EXPRESS_CHECKOUT' || strtoupper($txn_type) == 'VIRTUAL_TERMINAL' || strtoupper($txn_type) == 'WEB_ACCEPT' || strtoupper($txn_type) == 'SEND_MONEY' || strtoupper($txn_type) == 'INVOICE_PAYMENT' || strtoupper($txn_type) == 'PRO_HOSTED' )) {

            $newposted['txn_type_own'] = 'orders';
        } elseif (strtoupper($transaction_type) == 'ADAPTIVE PAYMENT PREAPPROVAL' || strtoupper($transaction_type) == 'ADAPTIVE PAYMENT PAY' || !empty($account_key)) {

            $newposted['txn_type_own'] = 'adaptive_paments';
        } else {

            $newposted['txn_type_own'] = 'other';
        }

        return $newposted;
    }

    /**
     * IPN validation request for adaptive paments
     *
     * @param unknown_type $posted
     * @return unknown
     * @since    1.0.4
     */
    public function paypal_ipn_for_wordpress_check_adaptive_paments_is_vlidate($posted = null) {
        $return = array();
        $txn_type = (isset($posted['txn_type'])) ? $posted['txn_type'] : '';
        $reason_code = (isset($posted['reason_code'])) ? $posted['reason_code'] : '';
        $payment_status = (isset($posted['payment_status'])) ? $posted['payment_status'] : '';
        $account_key = (isset($posted['account_key'])) ? $posted['account_key'] : '';
        $transaction_type = (isset($posted['transaction_type'])) ? $posted['transaction_type'] : '';

        if (strtoupper($transaction_type) == 'ADAPTIVE PAYMENT PREAPPROVAL' || strtoupper($transaction_type) == 'ADAPTIVE PAYMENT PAY' || !empty($account_key)) {

            $raw_post_data = file_get_contents('php://input');
            $raw_post_array = explode('&', $raw_post_data);

            $myPost = array();

            foreach ($raw_post_array as $keyval) {
                $keyval = explode('=', $keyval);
                if (count($keyval) == 2)
                    $myPost[$keyval[0]] = urldecode($keyval[1]);
            }

            // read the post from PayPal system and add 'cmd'
            $req = 'cmd=_notify-validate';
            if (function_exists('get_magic_quotes_gpc')) {
                $get_magic_quotes_exists = true;
            }

            foreach ($myPost as $key => $value) {
                if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                    $value = urlencode(stripslashes($value));
                } else {
                    $value = urlencode($value);
                }
                $req .= "&$key=$value";
            }

            $is_sandbox = (isset($posted['test_ipn'])) ? 'yes' : 'no';

            if ('yes' == $is_sandbox) {
                $paypal_url = $this->testurl;
            } else {
                $paypal_url = $this->liveurl;
            }

            $ch = curl_init($paypal_url);
            if ($ch == FALSE) {
                return FALSE;
            }

            $is_enable_curl = function_exists('curl_init') ? true : false;

            if ($is_enable_curl == false) {
                if ('yes' == $this->debug) {
                    $this->log->add('paypal', "cURL is not enabled", true);
                }
            }


            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

            if ('yes' == $this->debug) {
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            }

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
            $res = curl_exec($ch);

            if (curl_errno($ch) != 0) { // cURL error
                if ('yes' == $this->debug) {
                    $this->log->add('paypal', "Can't connect to PayPal to validate IPN message: " . print_r(curl_error($ch), true));
                }
                curl_close($ch);
                exit;
            } else {
                if ('yes' == $this->debug) {
                    $this->log->add('paypal', 'HTTP response of validation request: ' . print_r($req, true));
                }
                curl_close($ch);
            }
            $tokens = explode("\r\n\r\n", trim($res));
            $res = trim(end($tokens));
            if (strcmp($res, "VERIFIED") == 0) {
                if ('yes' == $this->debug) {
                    $this->log->add('paypal', 'Verified IPN: ' . print_r($res, true));
                }
                return true;
            } else if (strcmp($res, "INVALID") == 0) {

                if ('yes' == $this->debug) {
                    $this->log->add('paypal', 'Invalid IPN: ' . print_r($res, true));
                }
                return false;
            }
        } else {
            return $return['validate'] = 'required_to_check';
        }
    }

    public function decodePayPalIPN() {
        $raw_post = file_get_contents("php://input");
        if (empty($raw_post)) {
            return array();
        } // else:
        $post = array();
        $pairs = explode('&', $raw_post);
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            $key = urldecode($key);
            $value = urldecode($value);
            // This is look for a key as simple as 'return_url' or as complex as 'somekey[x].property'
            preg_match('/(\w+)(?:\[(\d+)\])?(?:\.(\w+))?/', $key, $key_parts);
            switch (count($key_parts)) {
                case 4:
                    // Original key format: somekey[x].property
                    // Converting to $post[somekey][x][property]
                    if (!isset($post[$key_parts[1]])) {
                        $post[$key_parts[1]] = array($key_parts[2] => array($key_parts[3] => $value));
                    } else if (!isset($post[$key_parts[1]][$key_parts[2]])) {
                        $post[$key_parts[1]][$key_parts[2]] = array($key_parts[3] => $value);
                    } else {
                        $post[$key_parts[1]][$key_parts[2]][$key_parts[3]] = $value;
                    }
                    break;
                case 3:
                    // Original key format: somekey[x]
                    // Converting to $post[somkey][x]
                    if (!isset($post[$key_parts[1]])) {
                        $post[$key_parts[1]] = array();
                    }
                    $post[$key_parts[1]][$key_parts[2]] = $value;
                    break;
                default:
                    // No special format
                    $post[$key] = $value;
                    break;
            }
        }

        return $post;
    }

}
