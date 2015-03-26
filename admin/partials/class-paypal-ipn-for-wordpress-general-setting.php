<?php

/**
 * This class defines all code necessary to General Setting from admin side
 * @class       AngellEYE_Paypal_Ipn_For_Wordpress_General_Setting
 * @version	1.0.0
 * @package	paypal-ipn-for-wordpress/includes
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_General_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {

        add_action('paypal_ipn_for_wordpress_general_setting', array(__CLASS__, 'paypal_ipn_for_wordpress_general_setting'));
        add_action('paypal_ipn_for_wordpress_general_setting_save_field', array(__CLASS__, 'paypal_ipn_for_wordpress_general_setting_save_field'));
    }

    /**
     * paypal_ipn_for_wordpress_general_setting_save_field function used for save general setting field value
     * @since 1.0.0
     * @access public static
     * 
     */
    public static function paypal_ipn_for_wordpress_general_setting_save_field() {
        if (isset($_POST['general_setting_integration']) && !empty($_POST['general_setting_integration'])) {
            $paypal_ipn_for_wordpress_paypal_debug = (isset($_POST['paypal_ipn_for_wordpress_paypal_debug'])) ? stripslashes_deep($_POST['paypal_ipn_for_wordpress_paypal_debug']) : '';
            update_option('paypal_ipn_for_wordpress_paypal_debug', $paypal_ipn_for_wordpress_paypal_debug);
        }
    }

    /**
     * paypal_ipn_for_wordpress_general_setting function used for display general setting block from admin side
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_general_setting() {
        echo '<div class="wrap">';
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
            <table class="form-table" id="paypal_ipn_primary_url">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for="paypal_ipn_primary_url"><?php echo __('PayPal IPN Primary URL:', 'Option') ?></label></th>
                        <td class="forminp forminp-text">
                            <input type="text" class="large-text code" name="paypal_ipn_primary_url" value="<?php echo site_url('?AngellEYE_Paypal_Ipn_For_Wordpress&action=ipn_handler'); ?>" readonly>
                            <p class="description"><?php _e('Take a look at the '); ?> <a href="https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNSetup/" target="_blank">PayPal IPN Configuration Guide</a><?php _e(' for details on setting up IPN with this URL.'); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="paypal_ipn_for_wordpress_paypal_debug"><?php _e('Debug Log', 'Option'); ?>:</label>
                        </th>
                        <td class="forminp">
                            <?php if (defined('PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR')) { ?>
                                <?php if (@fopen(PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR . 'test-log.log', 'a')) { ?>
                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php echo __('Debug Log'); ?></span></legend>
                                        <label for="paypal_ipn_for_wordpress_paypal_debug">
                                            <input type="checkbox" <?php echo (get_option('paypal_ipn_for_wordpress_paypal_debug') == '1') ? 'checked="checked"' : '' ?> value="1" id="paypal_ipn_for_wordpress_paypal_debug" name="paypal_ipn_for_wordpress_paypal_debug" class=""><?php echo __('Enable logging'); ?></label><br>
                                        <p class="description"><?php echo __('Log PayPal events, such as IPN requests, inside'); ?> <code><?php echo PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR; ?> </code></p>
                                    </fieldset>
                                <?php } else { ?>
                                    <p><?php printf('<mark class="error">' . __('Log directory (<code>%s</code>) is not writable. To allow logging, make this writable or define a custom <code>PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR</code>.', 'Option') . '</mark>', PAYPAL_IPN_FOR_WORDPRESS_LOG_DIR); ?></p>
                                <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="general_setting_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
        echo '</div>';
    }

}

AngellEYE_Paypal_Ipn_For_Wordpress_General_Setting::init();
