<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    paypal-ipn-for-wordpress
 * @subpackage paypal-ipn-for-wordpress/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Activator {

    /**
     * Fired during plugin activation.
     * @since    1.0.0
     */
    public static function activate() {
        /**
         *  call create_files function when plugin active
         */
        self::create_files();
        /**
         * Log activation in Angell EYE database via web service.
         */
        $log_url = $_SERVER['HTTP_HOST'];
        $log_plugin_id = 5;
        $log_activation_status = 1;
        wp_remote_request('http://www.angelleye.com/web-services/wordpress/update-plugin-status.php?url=' . $log_url . '&plugin_id=' . $log_plugin_id . '&activation_status=' . $log_activation_status);
    }

    /**
     * Create files/directories
     */
    private function create_files() {
        // Install files and folders for uploading files and prevent hotlinking
        $upload_dir = wp_upload_dir();

        $files = array(
            array(
                'base' => PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all'
            ),
            array(
                'base' => PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR,
                'file' => 'index.html',
                'content' => ''
            )
        );

        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}
