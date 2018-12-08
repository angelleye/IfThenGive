<?php
/**
 *
 * Registers post types and taxonomies
 *
 * @class       AngellEYE_IfThenGive_Post_types
 * @version		0.1.0
 * @package		ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Post_types {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'ifthengive_register_post_types'), 5);
        add_action('add_meta_boxes', array(__CLASS__, 'ifthengive_add_meta_boxes'), 10);
        add_action('save_post', array(__CLASS__, 'ifthengive_save_data'));
        add_filter('manage_edit-ifthengive_goals_columns', array(__CLASS__, 'ifthengive_edit_ifthengive_columns'));
        add_action('manage_ifthengive_goals_posts_custom_column', array(__CLASS__, 'ifthengive_buttons_columns'), 10, 2);
        /* custom **/
        add_filter('post_row_actions',array(__CLASS__, 'my_action_row'), 10, 2);        
        add_action( 'admin_head', array( __CLASS__, 'admin_header' ) );        
        add_action( 'admin_menu', array(__CLASS__,'register_ifthengive_submenu_page' ));        
    }

    /**
     * admin_header method sets column width as per the data.
     * @since    0.1.0
     * @access   static
     */
    
    public static function admin_header() {   
        $page = ( isset($_GET['view'] ) ) ? esc_attr( $_GET['view'] ) : false;
        if( 'givers' == $page ){
            echo '<style type="text/css">';            
            echo '.wp-list-table .column-BillingAgreement { width: 15%; }';
            echo '.wp-list-table .column-DisplayName { width: 15%; }';
            echo '.wp-list-table .column-Email { width: 20%; }';  
            echo '.wp-list-table .column-PayPalInfo { width: 11%; }';
            echo '.wp-list-table .column-amount { width: 8%; }';            
            echo '.wp-list-table .column-BADate { width: 12%; }';
            echo '.wp-list-table .column-Status { width: 7%; }';
            echo '.wp-list-table .column-ITGAction { width: 9%; }';        
            echo '</style>';
            echo '<script>
            $(document).ready(function(){
                $(\'[data-toggle="popover"]\').popover();   
            });
            </script>';
        }
        elseif( 'ListTransactions' == $page || 'GetUsersTransactions' == $page){
            echo '<style type="text/css">';            
            echo '.wp-list-table .column-transactionId { width: 15%; }';      
            echo '.wp-list-table .column-user_display_name { width: 15%; }';
            echo '.wp-list-table .column-amount { width: 8%; }';
            echo '.wp-list-table .column-email { width: 25%; }';           
            echo '.wp-list-table .column-ppack { width: 11%; }';
            echo '.wp-list-table .column-Txn_date { width: 11%; }';
            echo '.wp-list-table .column-ppinfo { width: 11%; }';            
            echo '</style>';   
            echo '<script>
            $(document).ready(function(){
                $(\'[data-toggle="popover"]\').popover();   
            });
            </script>';
        }        
        else{
            return; 
        }
    }
    
    /**
     * ifthengive_register_post_types function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function ifthengive_register_post_types() {
        global $wpdb;        
        if (post_type_exists('ifthengive_goals')) {
            return;
        }                

        register_post_type('ifthengive_goals', apply_filters('ifthengive_register_post_types', array(
                    'labels' => array(
                        'name' => __('Goals', 'ifthengive'),
                        'singular_name' => __('Goals', 'ifthengive'),
                        'menu_name' => _x('IfThenGive', 'Admin menu name', 'ifthengive'),
                        'add_new' => __('Add Goal', 'ifthengive'),
                        'add_new_item' => __('Add New Goal', 'ifthengive'),
                        'edit' => __('Edit', 'ifthengive'),
                        'edit_item' => __('Edit Goal', 'ifthengive'),
                        'new_item' => __('New Goal', 'ifthengive'),
                        'view' => __('View Goal', 'ifthengive'),
                        'view_item' => __('View Goal', 'ifthengive'),
                        'search_items' => __('Search Goal', 'ifthengive'),
                        'not_found' => __('No Goal found', 'ifthengive'),
                        'not_found_in_trash' => __('No Goal found in trash', 'ifthengive'),
                        'parent' => __('Parent Goal', 'ifthengive')
                    ),                    
                    'description' => __('This is where you can create new Goal.', 'ifthengive'),
                    'public' => false,
                    'show_ui' => true,
                    'capability_type' => apply_filters('itg_capability_type','post'),
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'ifthengive_goals'),
                    'query_var' => true,
                    'menu_icon' => ITG_PLUGIN_URL . '/admin/images/dashicon-itg.png',
                    'supports' => array('title'),
                    'has_archive' => true,
                    'show_in_nav_menus' => true,
                    'show_in_rest' => true
                        )
                )
        );
    }    
    /**
     * ifthengive_edit_ifthengive_columns function
     * is use for register button shortcode column.
     * @param type $columns returns attribute for custom column.
     * @since 0.1.0
     * @access public
     */
    public static function ifthengive_edit_ifthengive_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Goal Name','ifthengive'),
            'shortcodes' => __('Shortcodes','ifthengive'),
            'createdby' => __('Created By','ifthengive'),
            'date' => __('Date','ifthengive')
        );

        return $columns;
    }

    /**
     * ifthengive_buttons_columns function is use
     * for write content in custom registered column.
     * @global type $post returns the post variable values.
     * @param type $column Column name in which we want to write content.
     * @param type $post_id Post id of post in which content will be written for
     * the column.
     * @since 0.1.0
     * @access public
     */
    public static function ifthengive_buttons_columns($column, $post_id) {
        global $post;
        switch ($column) {
            case 'shortcodes' :
                $shortcode_avalabilty = get_post_meta($post_id, 'trigger_name', true);
                if (isset($shortcode_avalabilty) && !empty($shortcode_avalabilty)) {
                    echo '[ifthengive_goal id=' . $post_id . ']';
                } else {
                    echo __('Not Available');
                }

                break;
            case 'createdby' :                                
                $author_id = get_post_field ('post_author', $post_id);
                $display_name = get_the_author_meta( 'display_name' , $author_id ); 
                echo $display_name;
                break;
        }
    }
    
    /**
     * ifthengive_add_meta_boxes function is use for
     * register metabox for ifthengive custom post type.
     * @since 0.1.0
     * @access public
     */
    public static function ifthengive_add_meta_boxes() {
        add_meta_box('ifthengive-meta-id', __('Goal','ifthengive'), array(__CLASS__, 'ifthengive_metabox'), 'ifthengive_goals', 'normal', 'high');
    }
    
     /**
     * ifthengive_metabox function is use for write data
     * in metabox.
     * @global type $post returns the post variable values.
     * @global type $post_ID returns the post id of a post.
     * @since 0.1.0
     * @access public
     */
    
    public static function ifthengive_metabox() {
        $action_request= isset($_REQUEST['view']) ? sanitize_text_field($_REQUEST['view']) : '';        
        if ($action_request==='true') {
            do_action('ifthengive_shortcode_interface');
        }       
        else{
            do_action('ifthengive_interface');
        }        
    }
    
    /**
     * ifthengive_save_data is use for display    
     * @global type $post returns the post variable values.
     * @global type $post_ID returns the post id of a post.
     * @since 0.1.0
     * @access public
     */
    public static function ifthengive_save_data() {

        global $post, $post_ID, $wpdb;        
        if (((isset($_POST['publish'])) || isset($_POST['save'])) && ($post->post_type == 'ifthengive_goals')) {                          
            update_post_meta($post_ID, 'trigger_name',sanitize_text_field($_REQUEST['post_title']));
            update_post_meta($post_ID, 'trigger_thing',sanitize_text_field($_POST['trigger_thing']));
            update_post_meta($post_ID, 'trigger_desc',sanitize_text_field($_POST['trigger_desc']));
            update_post_meta($post_ID, 'image_url',  sanitize_text_field($_POST['image_url']));                        
            if($_POST['fixed_radio']=='fixed'){
                $fixed_amount_input = filter_var(number_format($_POST['fixed_amount_input'],2,'.', ''),FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                update_post_meta($post_ID, 'amount','fixed');
                if(empty(trim($_POST['fixed_amount_input'])) || $fixed_amount_input == 0){
                    $fixed_amount_input=filter_var('1.00',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                }               
                update_post_meta($post_ID, 'fixed_amount_input',$fixed_amount_input);
            }
            elseif($_POST['fixed_radio']=='manual'){
                $manual_amount_input = filter_var(number_format($_POST['manual_amount_input'],2,'.', ''),FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                update_post_meta($post_ID, 'amount','manual');
                if(empty(trim($_POST['manual_amount_input'])) || $manual_amount_input == 0){
                    $manual_amount_input=filter_var('1.00',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                }
                update_post_meta($post_ID, 'manual_amount_input',$manual_amount_input);
            }
            else{
                $amountArray = array();                
                foreach ($_POST['option_amount'] as $amount) {
                    if(empty(trim($amount)) || $amount == 0 ){
                        $amountArray[] = filter_var('1.00',FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                    }
                    else{                        
                        $amountArray[] = filter_var(number_format($amount,2,'.', ''), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    }
                }
                update_post_meta($post_ID, 'amount','select');
                update_post_meta($post_ID, 'option_name',array_map('sanitize_text_field',$_POST['option_name']));
                update_post_meta($post_ID, 'option_amount',$amountArray);
            }
        }
        if(isset($_POST['publish']) && $_POST['post_type']=='ifthengive_goals'){
            $url =  add_query_arg( array(
                                'post' => $post_ID,
                                'action' => 'edit',
                                'view' => 'true',
                                'add_post_success' => 'true'
                            ), admin_url('post.php'));            
            wp_safe_redirect($url);
            exit;
        }elseif(isset($_POST['save'])  && $_POST['post_type']=='ifthengive_goals'){
            $url =  add_query_arg( array(
                                'post' => $post_ID,
                                'action' => 'edit',
                                'view' => 'true',
                                'update_post_success' => 'true'
                            ), admin_url('post.php'));            
            wp_safe_redirect($url);
            exit;
        }
    }

    /**
     * my_action_row adds new three links in the post type action
     * view,givers,transactions
     * @since 0.1.0
     * @access public
     */
    public static function my_action_row($actions, $post){
        //check for your post type
        if ($post->post_type == "ifthengive_goals") {           
            $actions['view'] = '<a href="'.site_url().'/wp-admin/post.php?post=' . $post->ID . '&action=edit&view=true">'.__('View','ifthengive').'</a>';
            $actions['givers'] = '<a href="'.site_url().'/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=' . $post->ID . '&view=givers&&orderby=BADate&order=asc">'.__('Givers','ifthengive').'</a>';
            $actions['transactions'] = '<a href="'.site_url().'/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=' . $post->ID . '&view=ListTransactions&orderby=Txn_date&order=desc">'.__('Transactions','ifthengive').'</a>';
        }
        return $actions;
    }

   /**
     * register_ifthengive_submenu_adds new submenu pages under the custom post type.
     * givers,transactions and disconnect interface.
     * @since 0.1.0
     * @access public
     */
    
    public static function register_ifthengive_submenu_page() {
        add_submenu_page( 
            null,
            __('ITG Givers Page', 'ifthengive'),
            __('ITG Givers Page', 'ifthengive'),
            apply_filters('itg_submenu_capability','manage_options'),
            __('ifthengive_givers', 'ifthengive'),
            array(__CLASS__,'ifthengive_givers_page_callback')
        );
        
        add_submenu_page(
            null,
            __('IfThenGive disconnect Page', 'ifthengive'),
            __('IfThenGive disconnect Page', 'ifthengive'),
            apply_filters('itg_submenu_capability','manage_options'),
            __('ifthengive_disconnect_paypal', 'ifthengive'),
            array(__CLASS__,'ifthengive_disconnect_paypal_page_callback')
        );
    }
    
   /**
     * ifthengive_givers_page_callback callback of the transaction and givers page.
     * givers,transactions and disconnect interface.
     * @since 0.1.0
     * @access public
     */    
    public static function ifthengive_givers_page_callback(){
        
         ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) 
            {
                    $('#adminmenu').find('li:first').removeClass('wp-has-current-submenu');
                    $('#adminmenu').find('li:first').removeClass('current');
                    $('#menu-posts-ifthengive_goals').addClass('wp-has-current-submenu current');                    
                    
            });     
        </script>
        <?php
        
        if(isset($_REQUEST['page']) && isset($_REQUEST['view'])){
            if($_REQUEST['page'] === 'ifthengive_givers' && $_REQUEST['view'] === 'givers'){
                do_action('ifthengive_givers_interface');
            }
            elseif($_REQUEST['page'] === 'ifthengive_givers' && $_REQUEST['view'] === 'ListTransactions'){
                do_action('ifthengive_list_transactions_interface');
            }
            elseif($_REQUEST['page'] === 'ifthengive_givers' && $_REQUEST['view'] === 'DoTransactions'){                
                do_action('ifthengive_do_transactions_interface');
            }
            elseif($_REQUEST['page'] === 'ifthengive_givers' && $_REQUEST['view'] === 'GetTransactionDetail'){
                do_action('ifthengive_get_transaction_detail');
            }
            elseif($_REQUEST['page'] === 'ifthengive_givers' && $_REQUEST['view'] === 'RetryFailedTransactions'){
                do_action('ifthengive_retry_failed_transactions_interface');
            }
            elseif($_REQUEST['page'] === 'ifthengive_givers' && $_REQUEST['view'] === 'GetUsersTransactions'){
                do_action('ifthengive_get_users_transactions_interface');
            }
            else{
                return '';    
            }
        }
        else{
            return '';
        }        
    }

    /**
     * ifthengive_givers_page_callback is the callback for the setting page
     * and display interface of the connect and disconnet with paypal page.
     * @since 0.1.0
     * @access public
     */   
    
    public static function ifthengive_disconnect_paypal_page_callback(){
        if(isset($_REQUEST['page']) && isset($_REQUEST['action'])){
            if($_REQUEST['page'] === 'ifthengive_disconnect_paypal' && $_REQUEST['action'] === 'true'){
                do_action('ifthengive_disconnect_interface');
            }
        }
    }    
}

AngellEYE_IfThenGive_Post_types::init();