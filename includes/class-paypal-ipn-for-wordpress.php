<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    paypal-ipn-for-wordpress
 * @subpackage paypal-ipn-for-wordpress/includes
 * @author     Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      AngellEYE_Paypal_Ipn_For_Wordpress_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'PayPal IPN for WordPress';
        $this->version = '1.0.4';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_constants();

        // register API endpoints
        add_action('init', array($this, 'add_endpoint'), 0);
        // handle paypal-ipn-for-wordpress-api endpoint requests
        add_action('parse_request', array($this, 'handle_api_requests'), 0);

        add_action('paypal_ipn_for_wordpress_api_ipn_handler', array($this, 'paypal_ipn_for_wordpress_api_ipn_handler'));

        /**
         * Add action links
         * http://stackoverflow.com/questions/22577727/problems-adding-action-links-to-wordpress-plugin
         */
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . PIW_PLUGIN_BASENAME ,array($this,'plugin_action_links'),10,4);
    }

    /**
     * Return the plugin action links.  This will only be called if the plugin
     * is active.
     *
     * @since 1.0.0
     * @param array $actions associative array of action names to anchor tags
     * @return array associative array of plugin action links
     */
    public function plugin_action_links($actions, $plugin_file, $plugin_data, $context)
    {
        $custom_actions = array(
            'configure' => sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=paypal-ipn-for-wordpress-option' ), __( 'Configure', 'paypal-ipn' ) ),
            'docs'      => sprintf( '<a href="%s" target="_blank">%s</a>', 'http://www.angelleye.com/category/docs/paypal-ipn-for-wordpress/?utm_source=paypal_ipn_for_wordpress&utm_medium=docs_link&utm_campaign=paypal_ipn_for_wordpress', __( 'Docs', 'paypal-ipn' ) ),
            'support'   => sprintf( '<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/plugin/paypal-ipn/', __( 'Support', 'paypal-ipn' ) ),
            'review'    => sprintf( '<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/view/plugin-reviews/paypal-ipn', __( 'Write a Review', 'paypal-ipn' ) ),
        );

        // add the links to the front of the actions list
        return array_merge( $custom_actions, $actions );
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - AngellEYE_Paypal_Ipn_For_Wordpress_Loader. Orchestrates the hooks of the plugin.
     * - AngellEYE_Paypal_Ipn_For_Wordpress_i18n. Defines internationalization functionality.
     * - AngellEYE_Paypal_Ipn_For_Wordpress_Admin. Defines all hooks for the dashboard.
     * - AngellEYE_Paypal_Ipn_For_Wordpress_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-ipn-for-wordpress-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-ipn-for-wordpress-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-paypal-ipn-for-wordpress-admin.php';

        /**
         * The class responsible for defining all function related to log file
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-ipn-for-wordpress-logger.php';

        /**
         * The class responsible for defining all action for logger
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-ipn-for-wordpress-paypal-helper.php';
        
        /**
         * The class responsible for defining all action for IPN forwarder related functon
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-ipn-for-wordpress-paypal-ipn-forwarder.php';
        
        

        $this->loader = new AngellEYE_Paypal_Ipn_For_Wordpress_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the AngellEYE_Paypal_Ipn_For_Wordpress_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new AngellEYE_Paypal_Ipn_For_Wordpress_i18n();
        $plugin_i18n->set_domain($this->get_plugin_name());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new AngellEYE_Paypal_Ipn_For_Wordpress_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'posts_where_request', $plugin_admin, 'paypal_ipn_for_wordpress_modify_wp_search' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    AngellEYE_Paypal_Ipn_For_Wordpress_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * API request - Trigger any API requests
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function handle_api_requests() {
        global $wp;

        if (isset($_GET['action']) && $_GET['action'] == 'ipn_handler') {
            $wp->query_vars['AngellEYE_Paypal_Ipn_For_Wordpress'] = $_GET['action'];
        }

        // paypal-ipn-for-wordpress-api endpoint requests
        if (!empty($wp->query_vars['AngellEYE_Paypal_Ipn_For_Wordpress'])) {

            // Buffer, we won't want any output here
            ob_start();

            // Get API trigger
            $api = strtolower(esc_attr($wp->query_vars['AngellEYE_Paypal_Ipn_For_Wordpress']));

            // Trigger actions
            do_action('paypal_ipn_for_wordpress_api_' . $api);

            // Done, clear buffer and exit
            ob_end_clean();
            die('1');
        }
    }

    /**
     * add_endpoint function.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function add_endpoint() {

        // paypal-ipn-for-wordpress API for PayPal gateway IPNs, etc
        add_rewrite_endpoint('AngellEYE_Paypal_Ipn_For_Wordpress', EP_ALL);
    }

    public function paypal_ipn_for_wordpress_api_ipn_handler() {

        /**
         * The class responsible for defining all actions related to paypal ipn listener 
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-ipn-for-wordpress-paypal-helper.php';
        $AngellEYE_Paypal_Ipn_For_Wordpress_Paypal_Helper_Object = new AngellEYE_Paypal_Ipn_For_Wordpress_Paypal_Helper();

        /**
         * The check_ipn_request function check and validation for ipn response
         */
        if ($AngellEYE_Paypal_Ipn_For_Wordpress_Paypal_Helper_Object->check_ipn_request()) {
            $AngellEYE_Paypal_Ipn_For_Wordpress_Paypal_Helper_Object->successful_request($IPN_status = true);
        } else {
            $AngellEYE_Paypal_Ipn_For_Wordpress_Paypal_Helper_Object->successful_request($IPN_status = false);
        }
    }

    /**
     * Define PAYPAL_IPN_FOR_WORDPRESS Constants
     */
    private function define_constants() {
        if (!defined('PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR')) {
            define('PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR', ABSPATH . 'paypal-ipn-logs/');
        }
    }

}
