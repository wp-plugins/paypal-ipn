<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name:       PayPal IPN for WordPress
 * Plugin URI:        http://www.angelleye.com/
 * Description:       A PayPal Instant Payment Notification toolkit that helps you automate tasks in real-time when transactions hit your PayPal account.
 * Version:           1.0.4
 * Author:            Angell EYE
 * Author URI:        http://www.angelleye.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       paypal-ipn
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    die; // Exit if accessed directly
}

/**
 *  define PIW_PLUGIN_DIR constant for global use
 */
 if (!defined('PIW_PLUGIN_DIR'))
    define('PIW_PLUGIN_DIR', dirname(__FILE__));

/**
 * define PIW_PLUGIN_URL constant for global use
 */
if (!defined('PIW_PLUGIN_URL'))
    define('PIW_PLUGIN_URL', plugin_dir_url(__FILE__));
 
 /**
  *  define log file path
  */
 if (!defined('PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR')) {
	define('PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR', ABSPATH . 'paypal-ipn-logs/');
 }
 
 /**
  * define plugin basename
  */
 if (!defined('PIW_PLUGIN_BASENAME')) {
    define( 'PIW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
 }

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-paypal-ipn-for-wordpress-activator.php
 */
function activate_paypal_ipn_for_wordpress() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-ipn-for-wordpress-activator.php';
    AngellEYE_Paypal_Ipn_For_Wordpress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-paypal-ipn-for-wordpress-deactivator.php
 */
function deactivate_paypal_ipn_for_wordpress() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-ipn-for-wordpress-deactivator.php';
    AngellEYE_Paypal_Ipn_For_Wordpress_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_paypal_ipn_for_wordpress');
register_deactivation_hook(__FILE__, 'deactivate_paypal_ipn_for_wordpress');

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-paypal-ipn-for-wordpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_paypal_ipn_for_wordpress() {

    $plugin = new AngellEYE_Paypal_Ipn_For_Wordpress();
    $plugin->run();
}

run_paypal_ipn_for_wordpress();
