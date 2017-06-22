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
class AngellEYE_Give_When_Post_type_Sign_Up {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'give_when_register_post_type_sign_up'), 5);
        add_filter('manage_edit-give_when_sign_up_columns', array(__CLASS__, 'give_when_edit_give_when_sign_up_columns'));
        add_action('manage_give_when_sign_up_posts_custom_column', array(__CLASS__, 'give_when_sign_up_columns'), 10, 2);
        add_filter( 'manage_edit-give_when_sign_up_sortable_columns', array(__CLASS__, 'give_when_event_custom_columns_sortable'));
        add_filter( 'views_edit-give_when_sign_up', array(__CLASS__, 'give_when_add_button_to_views') );
        //add_action( 'admin_head-edit.php', array(__CLASS__,'give_when_move_custom_button'));
    }
    
    /**
     * give_when_register_post_type_sign_up function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_register_post_type_sign_up() {
        global $wpdb;
        if (post_type_exists('give_when_sign_up')) {
            return;
        }

        do_action('give_when_register_post_type_sign_up');

        register_post_type('give_when_sign_up', apply_filters('give_when_register_post_type_sign_up', array(
                    'labels' => array(
                        'name' => __('Give When Sign up', 'angelleye_give_when'),
                        'singular_name' => __('Give When Sign up', 'angelleye_give_when'),
                        'menu_name' => _x('Give When Sign up', 'Admin menu name', 'angelleye_give_when'),                        
                        'edit' => __('Edit', 'angelleye_give_when'),
                        'edit_item' => __('View Give When Sign up', 'angelleye_give_when'),
                        'new_item' => __('New Give When Sign up', 'angelleye_give_when'),
                        'view' => __('View Give When Sign up', 'angelleye_give_when'),
                        'view_item' => __('View Give When Sign up', 'angelleye_give_when'),
                        'search_items' => __('Search Give When Sign up', 'angelleye_give_when'),
                        'not_found' => __('No users found', 'angelleye_give_when'),
                        'not_found_in_trash' => __('No users found in trash', 'angelleye_give_when'),
                        'parent' => __('Parent Give When Sign up', 'angelleye_give_when')
                    ),
                    'description' => __('This is where you can create new Give When Sign up.', 'angelleye_give_when'),
                    'public' => false,
                    'show_ui' => true,
                    'capability_type' => 'post',
                    'capabilities' => array(
                        'create_posts' => 'do_not_allow', // false < WP 4.5, credit @Ewout
                    ),
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'give_when_sign_up'),
                    'query_var' => false,
                    'menu_icon' => 'dashicons-editor-table',
                    'supports' => array('title'),
                    'has_archive' => true,
                    'show_in_nav_menus' => false
                        )
                )
        );
    }
    
     /**
      * give_when_edit_give_when_sign_up_columns function
      * display relationships of Goal and Givers.
      * @param type $columns returns attribute for custom column.
      * @since 1.0.0
      * @access public
      */
     public static function give_when_edit_give_when_sign_up_columns($columns) {         
         $columns = array(
             'cb' => '<input type="checkbox" />',
             'goal_name' => __('Goal Name'),
             'billagreement' => __('Billing Agreement ID'),
             'user' => __('Givers'),
             'amount' => __('Amount'),
             'paypal_payer' => __('PayPal Payer ID')             
         ); 
        return $columns;
     }
     
     /**
     * give_when_sign_up_columns function is use
     * for write content in custom registered column.
     * @global type $post returns the post variable values.
     * @param type $column Column name in which we want to write content.
     * @param type $post_id Post id of post in which content will be written for
     * the column.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_sign_up_columns($column, $post_id) {                
        global $post;
        
        $metadata = get_post_meta($post_id);
        $amount_meta = $metadata['give_when_signup_amount'][0];
        $wp_user_id_meta = $metadata['give_when_signup_wp_user_id'][0];
        $wp_goal_id_meta = $metadata['give_when_signup_wp_goal_id'][0];
                
        $goal_name = get_post_meta($wp_goal_id_meta,'trigger_name',true);
        
        $user = get_userdata( $wp_user_id_meta );
        $billing_agreement_id = get_user_meta($wp_user_id_meta,'give_when_gec_billing_agreement_id',true);
        $paypal_payer_id = get_user_meta($wp_user_id_meta,'give_when_gec_payer_id',true);        
            
        
        switch ($column) {
            case 'goal_name' :
                  echo isset($goal_name) ? $goal_name : '-';
                break;
            case 'billagreement' :
                  echo isset($billing_agreement_id) ? $billing_agreement_id : '';  
                break;
            case 'user' :
                echo isset($user->user_login) ? $user->user_login : '';
                break;            
            case 'amount' :
                echo isset($amount_meta) ? '$'.$amount_meta : '';
                break;
            case 'paypal_payer' :
                echo isset($paypal_payer_id) ? $paypal_payer_id : '';
                break;
        }            
    }
    
    /**
    * Make custom columns sortable.
    * 
    * @access public
    * @param Array $columns The original columns
    * @return Array $columns The filtered columns
    */
    public static function give_when_event_custom_columns_sortable( $columns ) {
        // Add our columns to $columns array
        $columns['goal_name'] = 'goal_name';
        $columns['billagreement'] = 'billagreement';
        $columns['user'] = 'user';
        $columns['amount'] = 'amount';
        $columns['paypal_payer'] = 'paypal_payer';
        return $columns;
    }
    
    public static function give_when_add_button_to_views( $views )
    {
        $views['my-button'] = '<button id="give_when_get_payment_button" type="button"  title="Update from Provider" class="button button-primary">Get Payments</button>';
        return $views;
    }
    
    public static function give_when_move_custom_button()
    {
        global $current_screen;
        // Not our post type, exit earlier
        if( 'give_when_sign_up' != $current_screen->post_type )
            return;
        ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) 
            {
                $('#give_when_get_payment_button').prependTo('span.displaying-num');    
            });     
        </script>
        <?php 
    }
}

AngellEYE_Give_When_Post_type_sign_up::init();