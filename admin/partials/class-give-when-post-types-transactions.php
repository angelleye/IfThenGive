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
class AngellEYE_Give_When_Post_type_Transactions {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'give_when_register_post_type_transactions'), 5);                          
    }

    /**
     * give_when_register_post_type_transactions function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_register_post_type_transactions() {
        global $wpdb;
        if (post_type_exists('gw_transactions')) {
            return;
        }

        do_action('give_when_register_post_type_transactions');

        register_post_type('gw_transactions', apply_filters('give_when_register_post_type_transactions', array(
                    'labels' => array(
                        'name' => __('Give When Transactions', 'angelleye_give_when'),
                        'singular_name' => __('Give When Transactions', 'angelleye_give_when'),
                        'menu_name' => _x('Give When Transactions', 'Admin menu name', 'angelleye_give_when'),
                        'add_new' => __('Add Transaction', 'angelleye_give_when'),
                        'add_new_item' => __('Add New Transactions', 'angelleye_give_when'),
                        'edit' => __('Edit', 'angelleye_give_when'),
                        'edit_item' => __('View Transaction', 'angelleye_give_when'),
                        'new_item' => __('New Transaction', 'angelleye_give_when'),
                        'view' => __('View Transaction', 'angelleye_give_when'),
                        'view_item' => __('View Transaction', 'angelleye_give_when'),
                        'search_items' => __('Search Transaction', 'angelleye_give_when'),
                        'not_found' => __('No Transactions found', 'angelleye_give_when'),
                        'not_found_in_trash' => __('No Transactions found in trash', 'angelleye_give_when'),
                        'parent' => __('Parent Transaction', 'angelleye_give_when')
                    ),
                    'description' => __('This is where you can create new Goal.', 'angelleye_give_when'),
                    'public' => false,
                    'show_ui' => false,
                    'capability_type' => 'post',
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'gw_transactions'),
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

AngellEYE_Give_When_Post_type_Transactions::init();