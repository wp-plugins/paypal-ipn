<?php

/**
 *
 * Registers post types and taxonomies
 *
 * @class       AngellEYE_Paypal_Ipn_For_Wordpress_Post_types
 * @version	1.0.0
 * @package	paypal-ipn-for-wordpress
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Post_types {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_action('admin_print_scripts', array(__CLASS__, 'disable_autosave'));
        add_action('init', array(__CLASS__, 'paypal_ipn_for_wordpress_register_taxonomies'), 5);
        add_action('init', array(__CLASS__, 'paypal_ipn_for_wordpress_register_post_types'), 5);
        add_action('init', array(__CLASS__, 'paypal_ipn_for_wordpress_register_post_status'), 10);
        add_action('restrict_manage_posts', array(__CLASS__, 'paypal_ipn_for_wordpress_ipn_filter'), 10);
        add_action('add_meta_boxes', array(__CLASS__, 'paypal_ipn_for_wordpress_remove_meta_boxes'), 10);
        add_action('manage_edit-paypal_ipn_columns', array(__CLASS__, 'paypal_ipn_for_wordpress_add_paypal_ipn_columns'), 10, 2);
        add_action('manage_paypal_ipn_posts_custom_column', array(__CLASS__, 'paypal_ipn_for_wordpress_render_paypal_ipn_columns'), 2);
        add_filter('manage_edit-paypal_ipn_sortable_columns', array(__CLASS__, 'paypal_ipn_for_wordpress_paypal_ipn_sortable_columns'));
        add_action('pre_get_posts', array(__CLASS__, 'paypal_ipn_for_wordpress_ipn_column_orderby'));
        add_filter('views_edit-paypal_ipn', array(__CLASS__, 'paypal_ipn_for_wordpress_ipn_section_name'), 10, 1);
        add_action('add_meta_boxes', array(__CLASS__, 'paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_custome_fields'), 31);
        add_filter('paypal_ipn_for_wordpress_the_meta_key', array(__CLASS__, 'paypal_ipn_for_wordpress_the_meta_key_remove_raw_dump'), 10, 3);
        add_action('add_meta_boxes', array(__CLASS__, 'paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_serialized'), 31);
        add_action('add_meta_boxes', array(__CLASS__, 'paypal_ipn_for_wordpress_add_meta_boxes_provide_hook_function_snippets'), 31);
        add_filter('post_class', array(__CLASS__, 'paypal_ipn_for_wordpress_post_class_representation'), 10, 3);
    }

    /**
     * paypal_ipn_for_wordpress_register_taxonomies function.
     *
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_register_taxonomies() {

        if (taxonomy_exists('paypal_ipn_type')) {
            return;
        }

        do_action('paypal_ipn_for_wordpress_register_taxonomy');

        register_taxonomy('paypal_ipn_type', apply_filters('paypal-ipn-for-wordpress_taxonomy_objects_ipn_cat', array('paypal_ipn')), apply_filters('paypal-ipn-for-wordpress_taxonomy_args_ipn_cat', array(
            'hierarchical' => true,
            'label' => __('PayPal IPN Types', 'paypal-ipn'),
            'labels' => array(
                'name' => __('PayPal IPN Types', 'paypal-ipn'),
                'singular_name' => __('PayPal IPN Types', 'paypal-ipn'),
                'menu_name' => _x('PayPal IPN Types', 'Admin menu name', 'paypal-ipn'),
                'search_items' => __('Search PayPal IPN Types', 'paypal-ipn'),
                'all_items' => __('All PayPal IPN Types', 'paypal-ipn'),
                'parent_item' => __('Parent PayPal IPN Types', 'paypal-ipn'),
                'parent_item_colon' => __('Parent PayPal IPN Types:', 'paypal-ipn'),
                'edit_item' => __('Edit PayPal IPN Types', 'paypal-ipn'),
                'update_item' => __('Update PayPal IPN Types', 'paypal-ipn'),
                'add_new_item' => __('Add New PayPal IPN Types', 'paypal-ipn'),
                'new_item_name' => __('New PayPal IPN Types Name', 'paypal-ipn')
            ),
            'show_ui' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'paypal_ipn'),
            'update_count_callback' => '_update_post_term_count'
                ))
        );
    }

    /**
     * paypal_ipn_for_wordpress_register_post_types function
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_register_post_types() {
        global $wpdb;
        if (post_type_exists('paypal_ipn')) {
            return;
        }

        do_action('paypal_ipn_for_wordpress_register_post_type');

        register_post_type('paypal_ipn', apply_filters('paypal_ipn_for_wordpress_register_post_type_ipn', array(
            'labels' => array(
                'name' => __('PayPal IPN', 'paypal-ipn'),
                'singular_name' => __('PayPal IPN', 'paypal-ipn'),
                'menu_name' => _x('PayPal IPN', 'Admin menu name', 'paypal-ipn'),
                'add_new' => __('Add PayPal IPN', 'paypal-ipn'),
                'add_new_item' => __('Add New PayPal IPN', 'paypal-ipn'),
                'edit' => __('Edit', 'paypal-ipn'),
                'edit_item' => __('View PayPal IPN', 'paypal-ipn'),
                'new_item' => __('New PayPal IPN', 'paypal-ipn'),
                'view' => __('View PayPal IPN', 'paypal-ipn'),
                'view_item' => __('View PayPal IPN', 'paypal-ipn'),
                'search_items' => __('Search PayPal IPN', 'paypal-ipn'),
                'not_found' => __('No PayPal IPN found', 'paypal-ipn'),
                'not_found_in_trash' => __('No PayPal IPN found in trash', 'paypal-ipn'),
                'parent' => __('Parent PayPal IPN', 'paypal-ipn')
            ),
            'description' => __('This is where you can add new IPN to your store.', 'paypal-ipn'),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false, // Removes support for the "Add New" function
            ),
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
            'rewrite' => array('slug' => 'paypal_ipn'),
            'query_var' => true,
            'menu_icon' => PIW_PLUGIN_URL . 'admin/images/paypal-ipn-for-wordpress-icon.png',
            'supports' => array('', ''),
            'has_archive' => true,
            'show_in_nav_menus' => true
                        )
                )
        );
    }

    /**
     * Register our custom post statuses, used for paypal_ipn status
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_register_post_status() {
        global $wpdb;

        $ipn_post_status_list = self::paypal_ipn_for_wordpress_get_ipn_status();

        if (isset($ipn_post_status_list) && !empty($ipn_post_status_list)) {
            foreach ($ipn_post_status_list as $ipn_post_status) {
                $ipn_post_status_display_name = ucfirst(str_replace('_', ' ', $ipn_post_status));
                register_post_status($ipn_post_status, array(
                    'label' => _x($ipn_post_status_display_name, 'IPN status', 'paypal-ipn'),
                    'public' => ($ipn_post_status == 'trash') ? false : true,
                    'exclude_from_search' => false,
                    'show_in_admin_all_list' => ($ipn_post_status == 'trash') ? false : true,
                    'show_in_admin_status_list' => true,
                    'label_count' => _n_noop($ipn_post_status_display_name . ' <span class="count">(%s)</span>', $ipn_post_status_display_name . ' <span class="count">(%s)</span>', 'paypal-ipn')
                ));
            }
        }
    }

    /**
     * paypal_ipn_for_wordpress_ipn_filter function  used for IPN status and type that will display admin side.
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_ipn_filter() {
        global $typenow, $wp_query;

        if ($typenow == 'paypal_ipn') {
            ?>


            <select name="test_ipn" class="dropdown_post_status">
                <option value="-1"><?php _e('Show All Transaction', 'paypal-ipn'); ?></option>
                <?php
                $transaction_mode = array('0' => 'Live Transaction', '1' => 'Sandbox Transaction');
                foreach ($transaction_mode as $transaction_mode_key => $transaction_mode_value) :
                    if (isset($_GET['test_ipn']) && $_GET['test_ipn'] == $transaction_mode_key) {
                        $selected_status = 'selected="selected"';
                    } else {
                        $selected_status = '';
                    }
                    ?>
                    <option value="<?php echo esc_attr($transaction_mode_key); ?>" <?php echo esc_attr($selected_status); ?>><?php echo esc_html(ucwords($transaction_mode_value)); ?></option>
                <?php endforeach; ?>
            </select>

            <?php
            if ($typenow == 'paypal_ipn') {
                $args = array(
                    'type' => 'post',
                    'child_of' => 0,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => 0,
                    'hierarchical' => 1,
                    'taxonomy' => 'paypal_ipn_type',
                    'pad_counts' => false
                );
                ?>

                <select name="paypal_ipn_type" class="dropdown_product_cat">
                    <option value="0"><?php _e('Show all Payment Statuses', 'paypal-ipn'); ?></option>
                    <?php
                    $ipn_type_list = get_categories($args);
                    foreach ($ipn_type_list as $ipn_type) :
                        $selected_ipn_type = (isset($wp_query->query['paypal_ipn_type']) && $wp_query->query['paypal_ipn_type'] == $ipn_type->slug ? 'selected="selected"' : '');
                        ?>
                        <option value="<?php echo esc_attr($ipn_type->slug); ?>" <?php echo esc_attr($selected_ipn_type); ?>><?php echo esc_html($ipn_type->name); ?></option>
                    <?php endforeach; ?>
                </select>

                <?php
            }
        }
    }

    /**
     * paypal_ipn_for_wordpress_remove_meta_boxes function used for remove submitdiv meta_box for paypal_ipn custome post type
     * https://core.trac.wordpress.org/ticket/12706
     * I have remove submitdiv meta_box because it not support custome register_post_status like  Completed | Denied
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_remove_meta_boxes() {

        remove_meta_box('submitdiv', 'paypal_ipn', 'side');
        remove_meta_box('slugdiv', 'paypal_ipn', 'normal');
    }

    /**
     * paypal_ipn_for_wordpress_get_ipn_status helper function used for return IPN status
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_get_ipn_status() {
        global $wpdb;

        return $wpdb->get_col($wpdb->prepare("SELECT DISTINCT post_status FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s  ORDER BY post_status", 'paypal_ipn', 'auto-draft'));
    }

    /**
     * paypal_ipn_for_wordpress_get_ipn_status helper function used for return IPN status for filter
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_get_ipn_status_filter() {
        global $wpdb;

        return $wpdb->get_col($wpdb->prepare("SELECT DISTINCT post_status FROM {$wpdb->posts} WHERE post_type = %s AND post_status != %s AND post_status != %s ORDER BY post_status", 'paypal_ipn', 'auto-draft', 'not-available'));
    }

    /**
     * Define custom columns for IPN
     * @param  array $existing_columns
     * @since    1.0.0
     * @access   public
     * @return array
     */
    public static function paypal_ipn_for_wordpress_add_paypal_ipn_columns($existing_columns) {
        $columns = array();
        $columns['cb'] = '<input type="checkbox" />';
        $columns['title'] = _x('Transaction ID', 'paypal-ipn');
        $columns['invoice'] = _x('Invoice ID', 'paypal-ipn');
        $columns['payment_date'] = _x('Date', 'paypal-ipn');
        $columns['first_name'] = _x('Name / Company', 'paypal-ipn');
        $columns['mc_gross'] = __('Amount', 'paypal-ipn');
        $columns['txn_type'] = __('Transaction Type', 'paypal-ipn');
        $columns['payment_status'] = __('Payment Status', 'paypal-ipn');
        return $columns;
    }

    /**
     * paypal_ipn_for_wordpress_render_paypal_ipn_columns helper function used add own column in IPN listing
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_render_paypal_ipn_columns($column) {
        global $post;

        switch ($column) {
            case 'invoice' :
                $invoice = get_post_meta($post->ID, 'invoice', true);
                if (isset($invoice) && !empty($invoice)) {
                    echo esc_attr($invoice);
                } else {
                    $transaction_invoice_id = get_post_meta($post->ID, 'transaction_refund_id', true);
                    if (isset($transaction_invoice_id) && !empty($transaction_invoice_id)) {
                        echo esc_attr($transaction_invoice_id);
                    }
                }
                break;
            case 'payment_date' :
                $payment_date = esc_attr(get_post_meta($post->ID, 'payment_date', true));
                if (isset($payment_date) && !empty($payment_date)) {
                    echo $payment_date;
                } else {
                    $payment_date = esc_attr(get_post_meta($post->ID, 'payment_request_date', true));
                    if (isset($payment_date) && !empty($payment_date)) {
                        echo $payment_date;
                    }
                }
                break;
            case 'first_name' :
                echo esc_attr(get_post_meta($post->ID, 'first_name', true) . ' ' . get_post_meta($post->ID, 'last_name', true));
                echo (get_post_meta($post->ID, 'payer_business_name', true)) ? '<br />' . get_post_meta($post->ID, 'payer_business_name', true) : '';
                break;
            case 'mc_gross' :
                $mc_gross = get_post_meta($post->ID, 'mc_gross', true);
                if (isset($mc_gross) && !empty($mc_gross)) {
                    echo esc_attr($mc_gross);
                } else {
                    $transaction_amount = get_post_meta($post->ID, 'transaction_amount', true);
                    if (isset($transaction_amount) && !empty($transaction_amount)) {
                        echo esc_attr($transaction_amount);
                    }
                }
                break;
            case 'txn_type' :
                $txn_type = get_post_meta($post->ID, 'txn_type', true);
                if (isset($txn_type) && !empty($txn_type)) {
                    echo esc_attr($txn_type);
                } else {
                    $transaction_type = get_post_meta($post->ID, 'transaction_type', true);
                    if (isset($transaction_type) && !empty($transaction_type)) {
                        echo esc_attr($transaction_type);
                    }
                }
                break;

            case 'payment_status' :
                echo esc_attr(get_post_meta($post->ID, 'payment_status', true));
                break;
        }
    }

    /**
     * Disable the auto-save functionality for IPN.
     * @since    1.0.0
     * @access   public
     * @return void
     */
    public static function disable_autosave() {
        global $post;

        if ($post && get_post_type($post->ID) === 'paypal_ipn') {
            wp_dequeue_script('autosave');
        }
    }

    /**
     * paypal_ipn_for_wordpress_paypal_ipn_sortable_columns helper function used for make column shortable.
     * @since    1.0.0
     * @access   public
     * @return $columns
     */
    public static function paypal_ipn_for_wordpress_paypal_ipn_sortable_columns($columns) {

        $custom = array(
            'title' => 'txn_id',
            'invoice' => 'invoice',
            'payment_date' => 'payment_date',
            'first_name' => 'first_name',
            'mc_gross' => 'mc_gross',
            'txn_type' => 'txn_type',
            'payment_status' => 'payment_status',
            'payment_date' => 'payment_date'
        );

        return wp_parse_args($custom, $columns);
    }

    /**
     * paypal_ipn_for_wordpress_ipn_column_orderby helper function used for shorting query handler
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_ipn_column_orderby($query) {
        global $wpdb;
        if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'paypal_ipn' && isset($_GET['orderby']) && $_GET['orderby'] != 'None') {
            $query->query_vars['orderby'] = 'meta_value';
            $query->query_vars['meta_key'] = $_GET['orderby'];
            if (isset($query->query_vars['s']) && empty($query->query_vars['s'])) {
                $query->is_search = false;
            }
            if (isset($_GET['test_ipn']) && $_GET['test_ipn'] != '-1') {
                $query->set('meta_query', array(array('key' => 'test_ipn', 'value' => $_GET['test_ipn'], 'compare' => '=')));
            }
        } else {

            if (isset($_GET['test_ipn']) && $_GET['test_ipn'] != '-1') {
                $query->set('meta_query', array(array('key' => 'test_ipn', 'value' => $_GET['test_ipn'], 'compare' => '=')));
                if (isset($query->query_vars['s']) && empty($query->query_vars['s'])) {
                    $query->is_search = false;
                }
            }
        }
    }

    /**
     * paypal_ipn_for_wordpress_ipn_section_name helper function used for Make section name when it upto 20 characters.
     * @since    1.0.0
     * @access   public
     * @return array
     */
    public static function paypal_ipn_for_wordpress_ipn_section_name($sectionlist) {
        $sectiongroup = array('recurring_payments_p' => 'recurring_payments_profile', 'subscription_payment' => 'subscription_payments');
        if (!empty($sectionlist)) {
            foreach ($sectionlist as $sectionkey => $section) {
                if ($sectionkey == 'not-available') {
                    unset($sectionlist['not-available']);
                }
                if (array_key_exists($sectionkey, $sectiongroup)) {
                    $displayname = ucfirst(str_replace('_', ' ', $sectiongroup[$sectionkey]));
                    $displaysectionkey = ucfirst(str_replace('_', ' ', $sectionkey));
                    $section = str_replace($displaysectionkey, $displayname, $section);
                    $sectionlist[$sectiongroup[$sectionkey]] = ucwords($section);
                    unset($sectionlist[$sectionkey]);
                } else {
                    $sectionlist[$sectionkey] = ucwords($section);
                }
            }
        }
        return $sectionlist;
    }

    /**
     * paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_custome_fields function used for register own meta_box for display IPN custome filed read only
     * @since    1.0.0
     */
    public static function paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_custome_fields() {

        add_meta_box('paypal-ipn-ipn-data-custome-field', __('PayPal IPN Fields', 'paypal-ipn'), array(__CLASS__, 'paypal_ipn_for_wordpress_display_ipn_custome_fields'), 'paypal_ipn', 'normal', 'high');
    }

    /**
     * paypal_ipn_for_wordpress_display_ipn_custome_fields helper function used for display raw dump in html format
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_display_ipn_custome_fields() {
        if ($keys = get_post_custom_keys()) {
            echo "<div class='wrap'>";
            echo "<table class='widefat'><thead>
                        <tr>
                            <th>" . __('IPN Field Name', 'paypal-ipn') . "</th>
                            <th>" . __('IPN Field Value', 'paypal-ipn') . "</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>" . __('IPN Field Name', 'paypal-ipn') . "</th>
                            <th>" . __('IPN Field Value', 'paypal-ipn') . "</th>

                        </tr>
                    </tfoot>";
            foreach ((array) $keys as $key) {
                $keyt = trim($key);
                if (is_protected_meta($keyt, 'post'))
                    continue;
                $values = array_map('trim', get_post_custom_values($key));
                $value = implode($values, ', ');

                /**
                 * Filter the HTML output of the li element in the post custom fields list.
                 *
                 * @since 1.0.0
                 *
                 * @param string $html  The HTML output for the li element.
                 * @param string $key   Meta key.
                 * @param string $value Meta value.
                 */
                echo apply_filters('paypal_ipn_for_wordpress_the_meta_key', "<tr><th class='post-meta-key'>$key:</th> <td>$value</td></tr>", $key, $value);
            }
            echo "</table>";
            echo "</div>";
        }
    }

    /**
     * paypal_ipn_for_wordpress_the_meta_key_remove_raw_dump helper function used ignore specific key that will not display in raw dump
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_the_meta_key_remove_raw_dump($row, $key, $value) {
        if ($key != 'ipn data serialized') {
            return $row;
        }
    }

    /**
     * paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_serialized function used for register own meta_box for display IPN row data custome post type
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_serialized() {

        add_meta_box('paypal-ipn-for-wordpress-ipn-data-serialized', __('PayPal IPN Raw Data', 'paypal-ipn'), array(__CLASS__, 'paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_box'), 'paypal_ipn', 'advanced', 'high');
    }

    /**
     * paypal_ipn_for_wordpress_add_meta_boxes_provide_hook_function_snippets function used for register own meta_box for display Provide hook function snippets in the IPN detail page
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_add_meta_boxes_provide_hook_function_snippets() {

        add_meta_box('paypal-ipn-for-wordpress-ipn-function', __('Hook Function Snippet', 'paypal-ipn'), array(__CLASS__, 'paypal_ipn_for_wordpress_add_meta_boxes_hook_function_snippets'), 'paypal_ipn', 'advanced', 'high');
    }

    /**
     * paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_box helper function used for display IPN row data to bottom of detail section
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_add_meta_boxes_ipn_data_box() {
        global $post;
        $post_id = $post->ID;
        echo '<pre />';
        print_r(maybe_unserialize(get_post_meta($post_id, 'ipn data serialized', true)));
    }

    /**
     * paypal_ipn_for_wordpress_add_meta_boxes_hook_function_snippets helper function used for display IPN row data to bottom of detail section
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_add_meta_boxes_hook_function_snippets() {
        global $post;
        $post_id = $post->ID;

        $postedtest = maybe_unserialize(get_post_meta($post_id, 'ipn data serialized', true));
        $txn_type = get_post_meta($post_id, 'txn_type', true);
        $transaction_type = get_post_meta($post_id, 'transaction_type', true);

        if (isset($txn_type) && !empty($txn_type)) {
            $txn_type_name = $txn_type;
            $txn_type_name = strtolower(str_replace(' ', '_', $txn_type_name));
            $hookname = "'paypal_ipn_for_wordpress_txn_type_$txn_type_name'";
            $function_name_hook = "'process_$txn_type_name'";
            $function_name = "process_" . $txn_type_name;
        } elseif (isset($transaction_type) || $transaction_type == 'Adjustment' || $transaction_type = 'Adaptive Payment PAY' || $transaction_type = 'Adaptive Payment Pay') {
            $txn_type_name = $transaction_type;
            $txn_type_name = strtolower(str_replace(' ', '_', $txn_type_name));
            $hookname = "'paypal_ipn_for_wordpress_adaptive_$txn_type_name'";
            $function_name_hook = "'process_$txn_type_name'";
            $function_name = "process_" . $txn_type_name;
        }
        ?>
        <div>
            <h3><?php _e('Extending PayPal IPN for WordPress', 'paypal-ipn'); ?></h3>
            <p class="content_padding"><?php _e('PayPal IPN for WordPress provides a <a target="_blank" href="https://www.angelleye.com/paypal-ipn-for-wordpress-developer-guide/?utm_source=paypal_ipn_for_wordpress&utm_medium=docs_link_ipn_details&utm_campaign=paypal_ipn_for_wordpress">wide variety of developer hooks</a> that you can use within your theme or plugins to trigger your own function(s).  There are hooks available based on the IPN type as well as the payment status of a transaction.', 'paypal-ipn'); ?></p>
            <p class="content_padding"><?php _e('The code snippet below is a template that you can use to quickly setup your own hook functions.  You can see that it prepares all of the possible data for the IPN in PHP variables for you for easy access to the values within your code.  Just set the "hook_name" to the hook you would like to use to trigger the function, and set the "function_name" to the name of the function you’re using in your theme/plugin.', 'paypal-ipn'); ?></p>
        </div>
        <?php

        if (isset($txn_type_name) && !empty($txn_type_name)) :
            $code_prettyprint_txn_type = '';
            echo '<pre class="prettyprint" id="prettyprint">';
            $posted = '$posted';
            $code_prettyprint_txn_type = PHP_EOL . PHP_EOL;
            $code_prettyprint_txn_type .= "   add_action('hook_name', 'function_name', 10, 1);
        function function_name(" . $posted . ") {" . PHP_EOL . PHP_EOL;
            $code_prettyprint_txn_type .= "              // Parse data from IPN " . $posted . " array" . PHP_EOL . PHP_EOL;
            foreach ($postedtest as $postedtestkey => $postedtestvalue) {
                if (isset($postedtestvalue) && !empty($postedtestvalue)) {
                    $code_prettyprint_txn_type .= "              $" . $postedtestkey . " = " . "isset(" . "$" . "posted['" . $postedtestkey . "']) ? " . "$" . "posted['" . $postedtestkey . "'] : '';" . PHP_EOL;
                }
            }
            $code_prettyprint_txn_type .= "
            /**
            * At this point you can use the data to generate email notifications,
            * update your local database, hit 3rd party web services, or anything
            * else you might want to automate based on this type of IPN.
            */";
            $code_prettyprint_txn_type .= "
        }" . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

            print_r($code_prettyprint_txn_type);
            echo '</pre>';
        endif;
       
    }

    /**
     * paypal_ipn_for_wordpress_post_class_representation helper function used for IPN listing highlight when is invalid
     * @since    1.0.0
     * @access   public
     */
    public static function paypal_ipn_for_wordpress_post_class_representation($classes, $class, $postid) {
        global $post;

        if ($post->post_type == 'paypal_ipn') {
            $transaction_type = get_post_meta($post->ID, 'IPN_status', true);
            if ($transaction_type == 'Invalid') {
                $classes[] = 'warning';
            }

            $test_ipn = get_post_meta($post->ID, 'test_ipn', true);
            if ($test_ipn == '1') {
                $classes[] = 'sandbox_warning';
            }
        }

        return $classes;
    }

}

AngellEYE_Paypal_Ipn_For_Wordpress_Post_types::init();
