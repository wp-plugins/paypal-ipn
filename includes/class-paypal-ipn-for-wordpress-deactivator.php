<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    paypal-ipn-for-wordpress
 * @subpackage paypal-ipn-for-wordpress/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Deactivator {

    /**
     * Fired during plugin deactivation.
     * @since    1.0.0
     */
    public static function deactivate() {

        /**
         * Log deactivation in Angell EYE database via web service.
         */
        $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 5;
        $log_activation_status = 0;
        wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url='.$log_url.'&plugin_id='.$log_plugin_id.'&activation_status='.$log_activation_status);

    }

}
