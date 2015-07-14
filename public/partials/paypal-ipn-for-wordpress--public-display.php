<?php

/**
 * @class       AngellEYE_Paypal_Ipn_For_Wordpress_Public_Display
 * @version	1.0.0
 * @package	paypal-ipn-for-wordpress
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Paypal_Ipn_For_Wordpress_Public_Display {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        self::paypal_shopping_cart_for_wordPress_add_shortcode();
        add_filter('widget_text', 'do_shortcode');
    }

    public static function paypal_shopping_cart_for_wordPress_add_shortcode() {
        add_shortcode('paypal_ipn_list', array(__CLASS__, 'paypal_ipn_for_wordpress_paypal_ipn_list'));
        add_shortcode('paypal_ipn_data', array(__CLASS__, 'paypal_ipn_for_wordpress_paypal_ipn_paypal_ipn_data'));
    }

    public static function paypal_ipn_for_wordpress_paypal_ipn_paypal_ipn_data($atts) {

        extract(shortcode_atts(array(
            'txn_id' => 'txn_id',
            'field' => 'first_name',
                        ), $atts));


        ob_start();

        if (isset($atts['txn_id']) && !empty($atts['txn_id'])) {
            $args = array(
                'post_type' => 'paypal_ipn',
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => 'txn_id',
                        'value' => $atts['txn_id'],
                        'compare' => 'LIKE'
                    )
                )
            );

            $posts = get_posts($args);
            if (isset($posts[0]->ID) && !empty($posts[0]->ID)) {
                return get_post_meta($posts[0]->ID, $field, true);
                return ob_get_clean();
            } else {
                $mainhtml = "no records";
                return ob_get_clean();
            }
        } else {
            $mainhtml = "transaction id not found.";
            return ob_get_clean();
        }
    }

    public static function paypal_ipn_for_wordpress_paypal_ipn_list($atts) {

        extract(shortcode_atts(array(
            'txn_type' => 'any',
            'payment_status' => '',
            'limit' => 10,
            'field1' => 'txn_id',
            'field2' => 'payment_date',
            'field3' => 'first_name',
            'field4' => 'last_name',
            'field5' => 'mc_gross',
                        ), $atts));


        ob_start();


        if (empty($payment_status)) {
            $paypal_ipn_type = get_terms('paypal_ipn_type');
            $term_ids = wp_list_pluck($paypal_ipn_type, 'slug');
        } else {
            $term_ids = array('0' => $payment_status);
        }

        $args = array(
            'post_type' => 'paypal_ipn',
            'post_status' => $txn_type,
            'posts_per_page' => $limit,
            'tax_query' => array(
                array(
                    'taxonomy' => 'paypal_ipn_type',
                    'terms' => array_map('sanitize_title', $term_ids),
                    'field' => 'slug'
                )
            )
        );

        if (isset($atts) && !empty($atts)) {
            $start_loop = 1;
            $field_key_header = array();
            $field_key = array();
            foreach ($atts as $atts_key => $atts_value) {
                if (array_key_exists('field' . $start_loop, $atts)) {
                    $field_key_header['field' . $start_loop] = ucwords(str_replace('_', ' ', $atts['field' . $start_loop]));
                    $field_key['field' . $start_loop] = $atts['field' . $start_loop];
                }
                $start_loop = $start_loop + 1;
            }
        }

        $posts = get_posts($args);
        if ($posts) {
            $mainhtml = '';
            $output = '';
            $output .= '<table id="example" class="display" cellspacing="0" width="100%"><thead>';

            $thead = "<tr>";

            if(!empty($field_key_header))
            {
                foreach ($field_key_header as $field_key_header_key => $field_key_header_value) {
                    $thead .= "<th>" . $field_key_header_value . "</th>";
                }
            }

            $thead .= "</tr>";


            $thead_end = '</thead>';
            $tfoot_start = "<tfoot>";
            $tfoot_end = "</tfoot>";
            $mainhtml .= $output . $thead . $thead_end . $tfoot_start . $thead . $tfoot_end;
            $tbody_start = "<tbody>";
            $tbody = "";
            foreach ($posts as $post):
                $tbody .= "<tr>";

                if (isset($field_key) && !empty($field_key)) {
                    foreach ($field_key as $field_key_key => $field_key_value) {
                        $tbody .= "<td>" . get_post_meta($post->ID, $field_key_value, true) . "</td>";
                    }
                }

                $tbody .= "</tr>";
            endforeach;

            $tbody_end = "</tbody>";
            $mainhtml .= $tbody_start . $tbody . $tbody_end;
            $mainhtml .= "</table>";
            return $mainhtml;
            return ob_get_clean();
        } else {
            $mainhtml = "no records found";
            return ob_get_clean();
        }
    }

}

AngellEYE_Paypal_Ipn_For_Wordpress_Public_Display::init();
