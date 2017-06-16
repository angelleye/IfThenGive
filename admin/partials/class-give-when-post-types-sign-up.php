<?php
/**
 *
 * Registers post types and taxonomies
 *
 * @class       AngellEYE_Give_When_Post_types
 * @version		1.0.0
 * @package		give-when
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_Post_type_Billing_Agreement {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'give_when_register_post_type_billing_agreement'), 5);
        add_filter('manage_edit-give_when_paypal_ba_columns', array(__CLASS__, 'give_when_edit_give_when_paypal_ba_columns'));
        add_action('manage_give_when_paypal_ba_posts_custom_column', array(__CLASS__, 'give_when_ba_columns'), 10, 2);
        //add_action('add_meta_boxes', array(__CLASS__, 'give_when_add_meta_boxes'), 10);
        //add_action('save_post', array(__CLASS__, 'give_when_save_data'));                        
    }

        /**
     * give_when_edit_give_when_columns function
     * is use for register button shortcode column.
     * @param type $columns returns attribute for custom column.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_edit_give_when_paypal_ba_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Billing Agreement ID'),
            'giver' => __('Giver Name'),
            'email' => __('email'),
            'amount' => __('Amount'),
            'paypal_payer' => __('PayPal Payer ID')
        );

        return $columns;
    }
    
    /**
     * give_when_register_post_type_billing_agreement function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_register_post_type_billing_agreement() {
        global $wpdb;
        if (post_type_exists('give_when_paypal_ba')) {
            return;
        }

        do_action('give_when_register_post_type_billing_agreement');

        register_post_type('give_when_paypal_ba', apply_filters('give_when_register_post_type_billing_agreement', array(
                    'labels' => array(
                        'name' => __('Give When Billing Agreements', 'angelleye_give_when'),
                        'singular_name' => __('Give When Billing Agreements', 'angelleye_give_when'),
                        'menu_name' => _x('Give When Billing Agreements', 'Admin menu name', 'angelleye_give_when'),
                        'add_new' => __('Add Billing Agreements', 'angelleye_give_when'),
                        'add_new_item' => __('Add New Billing Agreements', 'angelleye_give_when'),
                        'edit' => __('Edit', 'angelleye_give_when'),
                        'edit_item' => __('View Billing Agreements', 'angelleye_give_when'),
                        'new_item' => __('New Billing Agreements', 'angelleye_give_when'),
                        'view' => __('View Billing Agreements', 'angelleye_give_when'),
                        'view_item' => __('View Billing Agreements', 'angelleye_give_when'),
                        'search_items' => __('Search Billing Agreements', 'angelleye_give_when'),
                        'not_found' => __('No Goal found', 'angelleye_give_when'),
                        'not_found_in_trash' => __('No Goal found in trash', 'angelleye_give_when'),
                        'parent' => __('Parent Goal', 'angelleye_give_when')
                    ),
                    'description' => __('This is where you can create new Goal.', 'angelleye_give_when'),
                    'public' => false,
                    'show_ui' => true,
                    'capability_type' => 'post',
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'give_when_paypal_ba'),
                    'query_var' => true,
                    'menu_icon' => 'dashicons-editor-table',
                    'supports' => array('title'),
                    'has_archive' => true,
                    'show_in_nav_menus' => false
                        )
                )
        );
    }
    
        /**
     * give_when_ba_columns function is use
     * for write content in custom registered column.
     * @global type $post returns the post variable values.
     * @param type $column Column name in which we want to write content.
     * @param type $post_id Post id of post in which content will be written for
     * the column.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_ba_columns($column, $post_id) {
        global $post;
        switch ($column) {
            case 'email' :
                $email = get_post_meta($post_id, 'give_when_gec_email',true);
                if(!empty($email)){
                    echo $email;
                }
                else{
                    echo '-';
                }
                break;
            case 'giver' :
                $fname = get_post_meta($post_id, 'give_when_gec_first_name',true);
                $lname = get_post_meta($post_id, 'give_when_gec_last_name',true);
                if(!empty($fname) || !empty($lname)){
                    echo $fname.' '.$lname;
                }
                else{
                    echo '-';
                }
                break;
            case 'amount' :
                $amount = get_post_meta($post_id, 'give_when_gec_amount',true);
                echo !empty($amount) ? number_format($amount,2) : '-';
                break;  
            case 'paypal_payer':
                $pp_payer = get_post_meta($post_id, 'give_when_gec_payer_id',true);
                echo !empty($pp_payer) ? $pp_payer : '-';
                break;
        }
    }
    
    /**
     * give_when_save_data is use for display    
     * @global type $post returns the post variable values.
     * @global type $post_ID returns the post id of a post.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_save_data() {

        global $post, $post_ID, $wpdb;       
        //$give_when_notice = get_post_meta($post_ID, 'paypal_wp_button_manager_success_notice', true);                                        
        if (((isset($_POST['publish'])) || isset($_POST['save'])) && ($post->post_type == 'give_when_goals')) {                          
            update_post_meta($post_ID, 'trigger_name',$_POST['trigger_name']);
            update_post_meta($post_ID, 'trigger_desc',$_POST['trigger_desc']);
            update_post_meta($post_ID, 'image_url',$_POST['image_url']);            
            if($_POST['fixed_radio']=='fixed'){
                update_post_meta($post_ID, 'amount','fixed');
                update_post_meta($post_ID, 'fixed_amount_input',$_POST['fixed_amount_input']);
            }
            else{
                update_post_meta($post_ID, 'amount','select');
                update_post_meta($post_ID, 'option_name',$_POST['option_name']);
                update_post_meta($post_ID, 'option_amount',$_POST['option_amount']);
            }
        }
        
    }
    
    
}

//AngellEYE_Give_When_Post_type_Billing_Agreement::init();