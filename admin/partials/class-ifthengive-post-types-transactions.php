<?php
/**
 *
 * Registers post types and taxonomies
 *
 * @class       AngellEYE_IfThenGive_Post_type_Transactions
 * @version		1.0.0
 * @package		ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Post_type_Transactions {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'ifthengive_register_post_type_transactions'), 5);                          
    }

    /**
     * ifthengive_register_post_type_transactions function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function ifthengive_register_post_type_transactions() {
        global $wpdb;
        if (post_type_exists('itg_transactions')) {
            return;
        }        

        register_post_type('itg_transactions', apply_filters('ifthengive_register_post_type_transactions', array(
                    'labels' => array(
                        'name' => __('Transactions', 'ifthengive'),
                        'singular_name' => __('Transactions', 'ifthengive'),
                        'menu_name' => _x('Transactions', 'Admin menu name', 'ifthengive'),
                        'add_new' => __('Add Transaction', 'ifthengive'),
                        'add_new_item' => __('Add New Transactions', 'ifthengive'),
                        'edit' => __('Edit', 'ifthengive'),
                        'edit_item' => __('View Transaction', 'ifthengive'),
                        'new_item' => __('New Transaction', 'ifthengive'),
                        'view' => __('View Transaction', 'ifthengive'),
                        'view_item' => __('View Transaction', 'ifthengive'),
                        'search_items' => __('Search Transaction', 'ifthengive'),
                        'not_found' => __('No Transactions found', 'ifthengive'),
                        'not_found_in_trash' => __('No Transactions found in trash', 'ifthengive'),
                        'parent' => __('Parent Transaction', 'ifthengive')
                    ),
                    'description' => __('This is where you can create new Goal.', 'ifthengive'),
                    'public' => false,
                    'show_ui' => false,
                    'capability_type' => 'post',
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'itg_transactions'),
                    'query_var' => true,
                    'menu_icon' => 'dashicons-editor-table',
                    'supports' => array('title'),
                    'has_archive' => true,
                    'show_in_nav_menus' => false
                        )
                )
        );
    }    
}

AngellEYE_IfThenGive_Post_type_Transactions::init();