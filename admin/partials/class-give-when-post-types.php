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
     * @since    0.1.0
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
        //add_action( 'admin_head', array( __CLASS__, 'admin_header' ) );
    }

    
//    public static function admin_header() {   
//        $page = ( isset($_GET['view'] ) ) ? esc_attr( $_GET['view'] ) : false;
//        if( 'givers' != $page )
//          return; 
//
//        echo '<style type="text/css">';
//        echo '';
//        echo '.wp-list-table .column-id { width: 5%; }';
//        echo '.wp-list-table .column-booktitle { width: 40%; }';
//        echo '.wp-list-table .column-author { width: 35%; }';
//        echo '.wp-list-table .column-isbn { width: 20%; }';
//        echo '</style>';
//    }
    
    /**
     * give_when_register_post_types function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_register_post_types() {
        global $wpdb;
        if (post_type_exists('angelleye_give_when')) {
            return;
        }


        do_action('give_when_register_post_types');

        register_post_type('give_when_goals', apply_filters('give_when_register_post_types', array(
                    'labels' => array(
                        'name' => __('Give When Goals', 'angelleye_give_when'),
                        'singular_name' => __('Give When Goals', 'angelleye_give_when'),
                        'menu_name' => _x('Give When', 'Admin menu name', 'angelleye_give_when'),
                        'add_new' => __('Add Goal', 'angelleye_give_when'),
                        'add_new_item' => __('Add New Goal', 'angelleye_give_when'),
                        'edit' => __('Edit', 'angelleye_give_when'),
                        'edit_item' => __('View Goal', 'angelleye_give_when'),
                        'new_item' => __('New Goal', 'angelleye_give_when'),
                        'view' => __('View Goal', 'angelleye_give_when'),
                        'view_item' => __('View Goal', 'angelleye_give_when'),
                        'search_items' => __('Search Goal', 'angelleye_give_when'),
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
                    'rewrite' => array('slug' => 'give_when_goals'),
                    'query_var' => true,
                    'menu_icon' => 'dashicons-editor-table',
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
            'title' => __('Give Goal Name'),
            'shortcodes' => __('Shortcodes'),
            'date' => __('Date')
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
            case 'publisher' :
                echo get_post_meta($post_id, 'publisher', true);
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
        add_meta_box('give-when-meta-id', __('Give When Goal'), array(__CLASS__, 'give_when_metabox'), 'give_when_goals', 'normal', 'high');
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
        elseif($action_request==='givers'){
            do_action('give_when_givers_interface');
        }
        elseif($action_request==='DoTransactions'){
            do_action('give_when_do_transactions_interface');
        }
        elseif ($action_request==='ListTransactions') {
            do_action('give_when_list_transactions_interface');
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
        //$give_when_notice = get_post_meta($post_ID, 'paypal_wp_button_manager_success_notice', true);                                        
        if (((isset($_POST['publish'])) || isset($_POST['save'])) && ($post->post_type == 'give_when_goals')) {                          
            update_post_meta($post_ID, 'trigger_name',$_POST['trigger_name']);
            update_post_meta($post_ID, 'trigger_thing',$_POST['trigger_thing']);
            update_post_meta($post_ID, 'trigger_desc',$_POST['trigger_desc']);
            update_post_meta($post_ID, 'image_url',$_POST['image_url']);            
            if($_POST['fixed_radio']=='fixed'){
                update_post_meta($post_ID, 'amount','fixed');
                update_post_meta($post_ID, 'fixed_amount_input',$_POST['fixed_amount_input']);
            }
            elseif($_POST['fixed_radio']=='manual'){
                update_post_meta($post_ID, 'amount','manual');
            }
            else{
                update_post_meta($post_ID, 'amount','select');
                update_post_meta($post_ID, 'option_name',$_POST['option_name']);
                update_post_meta($post_ID, 'option_amount',$_POST['option_amount']);
            }
        }
        
    }

    public static function my_action_row($actions, $post){
        //check for your post type
        if ($post->post_type == "give_when_goals") {           
            $actions['view'] = '<a href="'.site_url().'/wp-admin/post.php?post=' . $post->ID . '&action=edit&view=true">View</a>';
            $actions['givers'] = '<a href="'.site_url().'/wp-admin/post.php?post=' . $post->ID . '&action=edit&view=givers">Givers</a>';
            $actions['transactions'] = '<a href="'.site_url().'/wp-admin/post.php?post=' . $post->ID . '&action=edit&view=ListTransactions">Transactions</a>';
        }
        return $actions;
    }
    
    
}

AngellEYE_Give_When_Post_types::init();