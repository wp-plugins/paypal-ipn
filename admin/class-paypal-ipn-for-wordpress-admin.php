<?php

/**
 * @class       AngellEYE_Paypal_Ipn_For_Wordpress_Admin
 * @version	1.0.0
 * @package	paypal-ipn-for-wordpress
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $plugin_name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();
    }

    /**
     * Register the stylesheets for the Dashboard.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         *
         * An instance of this class should be passed to the run() function
         * defined in AngellEYE_Paypal_Ipn_For_Wordpress_Admin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The AngellEYE_Paypal_Ipn_For_Wordpress_Admin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/paypal-ipn-for-wordpress-admin.css', array(), $this->version, 'all');
    }

    private function load_dependencies() {

        /**
         * The class responsible for defining all actions that occur in the Dashboard for IPN Listing
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-paypal-ipn-for-wordpress-post-types.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/paypal-ipn-for-wordpress-admin-display.php';

        /**
         * The class responsible for defining function for display Html element
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-paypal-ipn-for-wordpress-html-output.php';

        /**
         * The class responsible for defining function for display general setting tab
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-paypal-ipn-for-wordpress-general-setting.php';
    }

}
