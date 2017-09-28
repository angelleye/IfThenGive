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
class AngellEYE_Give_When_Post_types {
    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'give_when_register_post_types'), 5);
        add_action('add_meta_boxes', array(__CLASS__, 'give_when_add_meta_boxes'), 10);
        add_action('save_post', array(__CLASS__, 'give_when_save_data'));
        add_filter('manage_edit-give_when_goals_columns', array(__CLASS__, 'give_when_edit_give_when_columns'));
        add_action('manage_give_when_goals_posts_custom_column', array(__CLASS__, 'give_when_buttons_columns'), 10, 2);
        /* custom **/
        add_filter('post_row_actions',array(__CLASS__, 'my_action_row'), 10, 2);        
        add_action( 'admin_head', array( __CLASS__, 'admin_header' ) );        
        add_action( 'admin_menu', array(__CLASS__,'register_give_when_submenu_page' ));        
    }


    public static function admin_header() {   
        $page = ( isset($_GET['view'] ) ) ? esc_attr( $_GET['view'] ) : false;
        if( 'givers' == $page ){
            echo '<style type="text/css">';            
            echo '.wp-list-table .column-BillingAgreement { width: 15%; }';
            echo '.wp-list-table .column-DisplayName { width: 15%; }';
            echo '.wp-list-table .column-PayPalEmail { width: 25%; }';
            echo '.wp-list-table .column-amount { width: 7%; }';
            echo '.wp-list-table .column-PayPalPayerID { width: 11%; }';
            echo '.wp-list-table .column-BADate { width: 10%; }';
            echo '.wp-list-table .column-GWAction { width: 7%; }';        
            echo '</style>';
        }
        elseif( 'ListTransactions' == $page || 'GetUsersTransactions' == $page){
            echo '<style type="text/css">';            
            echo '.wp-list-table .column-transactionId { width: 13%; }';      
            echo '.wp-list-table .column-user_display_name { width: 15%; }';
            echo '.wp-list-table .column-amount { width: 7%; }';
            echo '.wp-list-table .column-user_paypal_email { width: 25%; }';
            echo '.wp-list-table .column-PayPalPayerID { width: 15%; }';
            echo '.wp-list-table .column-ppack { width: 11%; }';
            echo '.wp-list-table .column-Txn_date { width: 11%; }';            
            echo '</style>';            
        }        
        else{
            return; 
        }
    }
    
    /**
     * give_when_register_post_types function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_register_post_types() {
        global $wpdb;        
        if (post_type_exists('give_when_goals')) {
            return;
        }
        
        //do_action('give_when_register_post_types');

        register_post_type('give_when_goals', apply_filters('give_when_register_post_types', array(
                    'labels' => array(
                        'name' => __('GiveWhen Goals', 'givewhen'),
                        'singular_name' => __('GiveWhen Goals', 'givewhen'),
                        'menu_name' => _x('GiveWhen', 'Admin menu name', 'givewhen'),
                        'add_new' => __('Add Goal', 'givewhen'),
                        'add_new_item' => __('Add New Goal', 'givewhen'),
                        'edit' => __('Edit', 'givewhen'),
                        'edit_item' => __('View Goal', 'givewhen'),
                        'new_item' => __('New Goal', 'givewhen'),
                        'view' => __('View Goal', 'givewhen'),
                        'view_item' => __('View Goal', 'givewhen'),
                        'search_items' => __('Search Goal', 'givewhen'),
                        'not_found' => __('No Goal found', 'givewhen'),
                        'not_found_in_trash' => __('No Goal found in trash', 'givewhen'),
                        'parent' => __('Parent Goal', 'givewhen')
                    ),                    
                    'description' => __('This is where you can create new Goal.', 'givewhen'),
                    'public' => false,
                    'show_ui' => true,
                    'capability_type' => __('give_when_goals','givewhen'),                    
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'give_when_goals'),
                    'query_var' => true,
                    'menu_icon' => GW_PLUGIN_URL . '/admin/images/dashicon-gw.png',
                    'supports' => array('title'),
                    'has_archive' => true,
                    'show_in_nav_menus' => true
                        )
                )
        );
    }    
    /**
     * give_when_edit_give_when_columns function
     * is use for register button shortcode column.
     * @param type $columns returns attribute for custom column.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_edit_give_when_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('GiveWhen Goal Name','givewhen'),
            'shortcodes' => __('Shortcodes','givewhen'),
            'createdby' => __('Created By','givewhen'),
            'date' => __('Date','givewhen')
        );

        return $columns;
    }

    /**
     * give_when_buttons_columns function is use
     * for write content in custom registered column.
     * @global type $post returns the post variable values.
     * @param type $column Column name in which we want to write content.
     * @param type $post_id Post id of post in which content will be written for
     * the column.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_buttons_columns($column, $post_id) {
        global $post;
        switch ($column) {
            case 'shortcodes' :
                $shortcode_avalabilty = get_post_meta($post_id, 'trigger_name', true);
                if (isset($shortcode_avalabilty) && !empty($shortcode_avalabilty)) {
                    echo '[give_when_goal id=' . $post_id . ']';
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
     * give_trigger_add_meta_boxes function is use for
     * register metabox for give_when custom post type.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_add_meta_boxes() {
        add_meta_box('give-when-meta-id', __('GiveWhen Goal','givewhen'), array(__CLASS__, 'give_when_metabox'), 'give_when_goals', 'normal', 'high');
    }
    
     /**
     * give_when_metabox function is use for write data
     * in metabox.
     * @global type $post returns the post variable values.
     * @global type $post_ID returns the post id of a post.
     * @since 1.0.0
     * @access public
     */
    
    public static function give_when_metabox() {      
        $action_request= isset($_REQUEST['view']) ? $_REQUEST['view'] : '';        
        if ($action_request==='true') {
            do_action('give_when_shortcode_interface');
        }       
        else{
            do_action('give_when_interface');
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
        if (((isset($_POST['publish'])) || isset($_POST['save'])) && ($post->post_type == 'give_when_goals')) {                          
            update_post_meta($post_ID, 'trigger_name',$_REQUEST['post_title']);
            update_post_meta($post_ID, 'trigger_thing',$_POST['trigger_thing']);
            update_post_meta($post_ID, 'trigger_desc',$_POST['trigger_desc']);
            update_post_meta($post_ID, 'image_url',$_POST['image_url']);            
            if($_POST['fixed_radio']=='fixed'){
                update_post_meta($post_ID, 'amount','fixed');
                update_post_meta($post_ID, 'fixed_amount_input',$_POST['fixed_amount_input']);
            }
            elseif($_POST['fixed_radio']=='manual'){
                update_post_meta($post_ID, 'amount','manual');
                if(empty(trim($_POST['manual_amount_input']))){
                    $_POST['manual_amount_input']=50.00;
                }
                update_post_meta($post_ID, 'manual_amount_input',  number_format($_POST['manual_amount_input'],2));
            }
            else{
                update_post_meta($post_ID, 'amount','select');
                update_post_meta($post_ID, 'option_name',$_POST['option_name']);
                update_post_meta($post_ID, 'option_amount',$_POST['option_amount']);
            }
        }
        if(isset($_POST['publish']) && $_POST['post_type']=='give_when_goals'){
            $url = admin_url()."post.php?post=".$post_ID."&action=edit&view=true&add_post_success=true";
            wp_redirect( $url );
            exit;
        }elseif(isset($_POST['save'])  && $_POST['post_type']=='give_when_goals'){
            $url = admin_url()."post.php?post=".$post_ID."&action=edit&view=true&update_post_success=true";
            wp_redirect( $url );
            exit;
        }
    }

    public static function my_action_row($actions, $post){
        //check for your post type
        if ($post->post_type == "give_when_goals") {           
            $actions['view'] = '<a href="'.site_url().'/wp-admin/post.php?post=' . $post->ID . '&action=edit&view=true">'.__('View','givewhen').'</a>';
            $actions['givers'] = '<a href="'.site_url().'/wp-admin/edit.php?post_type=give_when_goals&page=give_when_givers&post=' . $post->ID . '&view=givers">'.__('Givers','givewhen').'</a>';
            $actions['transactions'] = '<a href="'.site_url().'/wp-admin/edit.php?post_type=give_when_goals&page=give_when_givers&post=' . $post->ID . '&view=ListTransactions">'.__('Transactions','givewhen').'</a>';
        }
        return $actions;
    }    
    
    public static function register_give_when_submenu_page() {
        add_submenu_page( 
            null,
            __('GiveWhen Givers Page', 'givewhen'),
            __('GiveWhen Givers Page', 'givewhen'),
            'edit_give_when_goalss',
            __('give_when_givers', 'givewhen'),
            array(__CLASS__,'give_when_givers_page_callback')
        );
        
        add_submenu_page(
            null,
            __('GiveWhen disconnect Page', 'givewhen'),
            __('GiveWhen disconnect Page', 'givewhen'),
            'edit_give_when_goalss',
            __('give_when_disconnect_paypal', 'givewhen'),
            array(__CLASS__,'give_when_disconnect_paypal_page_callback')
        );
    }
    
    public static function give_when_givers_page_callback(){
        
         ?>
        <script type="text/javascript">
            jQuery(document).ready( function($) 
            {
                    $('#adminmenu').find('li:first').removeClass('wp-has-current-submenu');
                    $('#adminmenu').find('li:first').removeClass('current');
                    $('#menu-posts-give_when_goals').addClass('wp-has-current-submenu current');                    
                    
            });     
        </script>
        <?php
        
        if(isset($_REQUEST['page']) && isset($_REQUEST['view'])){
            if($_REQUEST['page'] === 'give_when_givers' && $_REQUEST['view'] === 'givers'){
                do_action('give_when_givers_interface');
            }
            elseif($_REQUEST['page'] === 'give_when_givers' && $_REQUEST['view'] === 'ListTransactions'){
                do_action('give_when_list_transactions_interface');
            }
            elseif($_REQUEST['page'] === 'give_when_givers' && $_REQUEST['view'] === 'DoTransactions'){                
                do_action('give_when_do_transactions_interface');
            }
            elseif($_REQUEST['page'] === 'give_when_givers' && $_REQUEST['view'] === 'GetTransactionDetail'){
                do_action('give_when_get_transaction_detail');
            }
            elseif($_REQUEST['page'] === 'give_when_givers' && $_REQUEST['view'] === 'RetryFailedTransactions'){
                do_action('give_when_retry_failed_transactions_interface');
            }
            elseif($_REQUEST['page'] === 'give_when_givers' && $_REQUEST['view'] === 'GetUsersTransactions'){
                do_action('give_when_get_users_transactions_interface');
            }
            else{
                return '';    
            }
        }
        else{
            return '';
        }        
    }
    
    public static function give_when_disconnect_paypal_page_callback(){
        if(isset($_REQUEST['page']) && isset($_REQUEST['action'])){
            if($_REQUEST['page'] === 'give_when_disconnect_paypal' && $_REQUEST['action'] === 'true'){
                do_action('give_when_disconnect_interface');
            }
        }
    }    
}

AngellEYE_Give_When_Post_types::init();