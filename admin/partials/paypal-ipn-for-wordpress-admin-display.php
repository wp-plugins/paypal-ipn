<?php

/**
 * @class       AngellEYE_Paypal_Ipn_For_Wordpress_Admin_Display
 * @version	1.0.0
 * @package	paypal-ipn-for-wordpress
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Admin_Display {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
    }

    /**
     * add_settings_menu helper function used for add menu for pluging setting
     * @since    1.0.0
     * @access   public
     */
    public static function add_settings_menu() {
        add_options_page('PayPal IPN for WordPress Options', 'PayPal IPN', 'manage_options', 'paypal-ipn-for-wordpress-option', array(__CLASS__, 'paypal_ipn_for_wordpress_options'));
    }

    /**
     * paypal_ipn_for_wordpress_options helper will trigger hook and handle all the settings section
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_options() {
        $setting_tabs = apply_filters('paypal_ipn_for_wordpress_setting_tab', array('general' => 'General'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs as $name => $label)
                echo '<a href="' . admin_url('admin.php?page=paypal-ipn-for-wordpress-option&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
        </h2>
        <?php
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('paypal_ipn_for_wordpress_' . $setting_tabkey . '_setting_save_field');
                    do_action('paypal_ipn_for_wordpress_' . $setting_tabkey . '_setting');
                    break;
            }
        }
    }

}

AngellEYE_Paypal_Ipn_For_Wordpress_Admin_Display::init();
