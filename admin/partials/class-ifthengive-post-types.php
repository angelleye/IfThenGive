<?php
/**
 *
 * Registers post types and taxonomies
 *
 * @class       AngellEYE_IfThenGive_Post_types
 * @version		1.0.0
 * @package		ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Post_types {
    /**
     * Hook in methods
     * @since    1.0.0
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
            echo '.wp-list-table .column-ITGAction { width: 7%; }';        
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
                        'name' => __('Goals', ITG_TEXT_DOMAIN),
                        'singular_name' => __('Goals', ITG_TEXT_DOMAIN),
                        'menu_name' => _x('IfThenGive', 'Admin menu name', ITG_TEXT_DOMAIN),
                        'add_new' => __('Add Goal', ITG_TEXT_DOMAIN),
                        'add_new_item' => __('Add New Goal', ITG_TEXT_DOMAIN),
                        'edit' => __('Edit', ITG_TEXT_DOMAIN),
                        'edit_item' => __('View Goal', ITG_TEXT_DOMAIN),
                        'new_item' => __('New Goal', ITG_TEXT_DOMAIN),
                        'view' => __('View Goal', ITG_TEXT_DOMAIN),
                        'view_item' => __('View Goal', ITG_TEXT_DOMAIN),
                        'search_items' => __('Search Goal', ITG_TEXT_DOMAIN),
                        'not_found' => __('No Goal found', ITG_TEXT_DOMAIN),
                        'not_found_in_trash' => __('No Goal found in trash', ITG_TEXT_DOMAIN),
                        'parent' => __('Parent Goal', ITG_TEXT_DOMAIN)
                    ),                    
                    'description' => __('This is where you can create new Goal.', ITG_TEXT_DOMAIN),
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
                    'show_in_nav_menus' => true
                        )
                )
        );
    }    
    /**
     * ifthengive_edit_ifthengive_columns function
     * is use for register button shortcode column.
     * @param type $columns returns attribute for custom column.
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_edit_ifthengive_columns($columns) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Goal Name',ITG_TEXT_DOMAIN),
            'shortcodes' => __('Shortcodes',ITG_TEXT_DOMAIN),
            'createdby' => __('Created By',ITG_TEXT_DOMAIN),
            'date' => __('Date',ITG_TEXT_DOMAIN)
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
     * @since 1.0.0
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
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_add_meta_boxes() {
        add_meta_box('ifthengive-meta-id', __('Goal',ITG_TEXT_DOMAIN), array(__CLASS__, 'ifthengive_metabox'), 'ifthengive_goals', 'normal', 'high');
    }
    
     /**
     * ifthengive_metabox function is use for write data
     * in metabox.
     * @global type $post returns the post variable values.
     * @global type $post_ID returns the post id of a post.
     * @since 1.0.0
     * @access public
     */
    
    public static function ifthengive_metabox() {
        $action_request= isset($_REQUEST['view']) ? $_REQUEST['view'] : '';        
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
     * @since 1.0.0
     * @access public
     */
    public static function ifthengive_save_data() {

        global $post, $post_ID, $wpdb;        
        if (((isset($_POST['publish'])) || isset($_POST['save'])) && ($post->post_type == 'ifthengive_goals')) {                          
            update_post_meta($post_ID, 'trigger_name',$_REQUEST['post_title']);
            update_post_meta($post_ID, 'trigger_thing',$_POST['trigger_thing']);
            update_post_meta($post_ID, 'trigger_desc',$_POST['trigger_desc']);
            update_post_meta($post_ID, 'image_url',$_POST['image_url']);            
            if($_POST['fixed_radio']=='fixed'){
                update_post_meta($post_ID, 'amount','fixed');
                if(empty(trim($_POST['manual_amount_input']))){
                    $_POST['fixed_amount_input']=1.00;
                }
                update_post_meta($post_ID, 'fixed_amount_input',number_format($_POST['fixed_amount_input'],2));
            }
            elseif($_POST['fixed_radio']=='manual'){
                update_post_meta($post_ID, 'amount','manual');
                if(empty(trim($_POST['manual_amount_input']))){
                    $_POST['manual_amount_input']=1.00;
                }
                update_post_meta($post_ID, 'manual_amount_input',  number_format($_POST['manual_amount_input'],2));
            }
            else{
                update_post_meta($post_ID, 'amount','select');
                update_post_meta($post_ID, 'option_name',$_POST['option_name']);
                update_post_meta($post_ID, 'option_amount',$_POST['option_amount']);
            }
        }
        if(isset($_POST['publish']) && $_POST['post_type']=='ifthengive_goals'){
            $url = admin_url()."post.php?post=".$post_ID."&action=edit&view=true&add_post_success=true";
            wp_redirect( $url );
            exit;
        }elseif(isset($_POST['save'])  && $_POST['post_type']=='ifthengive_goals'){
            $url = admin_url()."post.php?post=".$post_ID."&action=edit&view=true&update_post_success=true";
            wp_redirect( $url );
            exit;
        }
    }

    public static function my_action_row($actions, $post){
        //check for your post type
        if ($post->post_type == "ifthengive_goals") {           
            $actions['view'] = '<a href="'.site_url().'/wp-admin/post.php?post=' . $post->ID . '&action=edit&view=true">'.__('View',ITG_TEXT_DOMAIN).'</a>';
            $actions['givers'] = '<a href="'.site_url().'/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=' . $post->ID . '&view=givers">'.__('Givers',ITG_TEXT_DOMAIN).'</a>';
            $actions['transactions'] = '<a href="'.site_url().'/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=' . $post->ID . '&view=ListTransactions">'.__('Transactions',ITG_TEXT_DOMAIN).'</a>';
        }
        return $actions;
    }    
    
    public static function register_ifthengive_submenu_page() {
        add_submenu_page( 
            null,
            __('ITG Givers Page', ITG_TEXT_DOMAIN),
            __('ITG Givers Page', ITG_TEXT_DOMAIN),
            apply_filters('itg_submenu_capability','manage_options'),
            __('ifthengive_givers', ITG_TEXT_DOMAIN),
            array(__CLASS__,'ifthengive_givers_page_callback')
        );
        
        add_submenu_page(
            null,
            __('IfThenGive disconnect Page', ITG_TEXT_DOMAIN),
            __('IfThenGive disconnect Page', ITG_TEXT_DOMAIN),
            apply_filters('itg_submenu_capability','manage_options'),
            __('ifthengive_disconnect_paypal', ITG_TEXT_DOMAIN),
            array(__CLASS__,'ifthengive_disconnect_paypal_page_callback')
        );
    }
    
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
    
    public static function ifthengive_disconnect_paypal_page_callback(){
        if(isset($_REQUEST['page']) && isset($_REQUEST['action'])){
            if($_REQUEST['page'] === 'ifthengive_disconnect_paypal' && $_REQUEST['action'] === 'true'){
                do_action('ifthengive_disconnect_interface');
            }
        }
    }    
}

AngellEYE_IfThenGive_Post_types::init();