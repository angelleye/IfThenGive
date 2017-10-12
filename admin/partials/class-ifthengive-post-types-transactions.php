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
                        'name' => __('Transactions', ITG_TEXT_DOMAIN),
                        'singular_name' => __('Transactions', ITG_TEXT_DOMAIN),
                        'menu_name' => _x('Transactions', 'Admin menu name', ITG_TEXT_DOMAIN),
                        'add_new' => __('Add Transaction', ITG_TEXT_DOMAIN),
                        'add_new_item' => __('Add New Transactions', ITG_TEXT_DOMAIN),
                        'edit' => __('Edit', ITG_TEXT_DOMAIN),
                        'edit_item' => __('View Transaction', ITG_TEXT_DOMAIN),
                        'new_item' => __('New Transaction', ITG_TEXT_DOMAIN),
                        'view' => __('View Transaction', ITG_TEXT_DOMAIN),
                        'view_item' => __('View Transaction', ITG_TEXT_DOMAIN),
                        'search_items' => __('Search Transaction', ITG_TEXT_DOMAIN),
                        'not_found' => __('No Transactions found', ITG_TEXT_DOMAIN),
                        'not_found_in_trash' => __('No Transactions found in trash', ITG_TEXT_DOMAIN),
                        'parent' => __('Parent Transaction', ITG_TEXT_DOMAIN)
                    ),
                    'description' => __('This is where you can create new Goal.', ITG_TEXT_DOMAIN),
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