<?php

/**
 * PayPal IPN Forwarder class
 *
 * This class defines all code necessary to IPN forwarder functionality 
 *
 * @since      1.0.0
 * @package    paypal-ipn-for-wordpress
 * @subpackage paypal-ipn-for-wordpress/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Ipn_Forwarder {

    /**
     * init for the Ipn_Forwarder.
     */
    public static function init() {

        add_action('paypal_ipn_for_wordpress_ipn_forwarding_handler', array(__CLASS__, 'paypal_ipn_for_wordpress_ipn_forwarding_handler'), 10, 1);

        // // Check PayPal Standard is enabled
        $woocommerce_paypal_settings = get_option('woocommerce_paypal_settings');
        
        if(isset($woocommerce_paypal_settings['enabled']) && $woocommerce_paypal_settings['enabled'] == 'yes') {
            
            add_filter('woocommerce_paypal_args', array(__CLASS__, 'paypal_ipn_for_wordpress_standard_parameters'), 10, 1);

            add_filter('paypal_ipn_for_wordpress_ipn_forwarding_setting', array(__CLASS__, 'paypal_ipn_for_wordpress_ipn_forwarding_setting'), 10, 1);
            
        }
    }

    /**
     * paypal_ipn_for_wordpress_ipn_forwarding_handler helper function used for IPN handler
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_ipn_forwarding_handler($posted) {

        $posted = stripslashes_deep($posted);

        $ipn_forwarding_setting_serialize = apply_filters('paypal_ipn_for_wordpress_ipn_forwarding_setting', maybe_unserialize(get_option('ipn_forwarding_setting')));

        if (isset($ipn_forwarding_setting_serialize) && !empty($ipn_forwarding_setting_serialize)) {

            foreach ($ipn_forwarding_setting_serialize as $serialize_key => $serialize_value) {

                if ((isset($serialize_value['paypal_ipn_url']) && !empty($serialize_value['paypal_ipn_url'])) && isset($serialize_value['active_inactive_checkbox']) && $serialize_value['active_inactive_checkbox'] == 'on') {

                    $params = array(
                        'body' => $posted,
                        'sslverify' => false,
                        'timeout' => 60,
                        'httpversion' => '1.1',
                        'compress' => false,
                        'decompress' => false,
                        'user-agent' => 'paypal-ipn/'
                    );

                    $response = wp_remote_post($serialize_value['paypal_ipn_url'], $params);
                }
            }
        }
    }

    /**
     * 
     * @param type $paypal_args
     * @return string
     */
    public static function paypal_ipn_for_wordpress_standard_parameters($paypal_args) {
        
        $paypal_args['bn'] = 'AngellEYE_SP_WooCommerce';
        update_option('woo_notify_url', site_url('?wc-api=WC_Gateway_Paypal'), true);
        $paypal_args['notify_url'] = site_url('?AngellEYE_Paypal_Ipn_For_Wordpress&action=ipn_handler');
        
        /**
         *  PayPal request args add to log file 
         */
        $debug = (get_option('paypal_ipn_for_wordpress_paypal_debug') == '1') ? 'yes' : 'no';

        if ('yes' == $debug) {
            $log = new AngellEYE_Paypal_Ipn_For_Wordpress_Logger();
            $log->add('paypal', 'PayPal Request args: ' . print_r($paypal_args, true));
        }

        return $paypal_args;
    }

    /**
     * 
     * @param type $ipn_forwarding_setting
     * @return type array
     */
    public static function paypal_ipn_for_wordpress_ipn_forwarding_setting($ipn_forwarding_setting) {

        $woo_notify_url = get_option('woo_notify_url');
        if (!empty($woo_notify_url)) {
            $ipn_forwarding_setting[] = array(
                'paypal_ipn_url' => $woo_notify_url,
                'active_inactive_checkbox' => 'on',
            );
        }
        return $ipn_forwarding_setting;
    }

}

AngellEYE_Paypal_Ipn_For_Wordpress_Ipn_Forwarder::init();
