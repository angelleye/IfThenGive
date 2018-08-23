<?php
/**
 *
 * Registers post types and taxonomies
 *
 * @class       AngellEYE_IfThenGive_Post_type_Sign_Up
 * @version		0.1.0
 * @package		ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Post_type_Sign_Up {
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'ifthengive_register_post_type_sign_up'), 5);
    }
    
    /**
     * ifthengive_register_post_type_sign_up function is user for register custom post type
     * @since    0.1.0
     * @access   public
     */
    public static function ifthengive_register_post_type_sign_up() {
        global $wpdb;
        if (post_type_exists('itg_sign_up')) {
            return;
        }
        register_post_type('itg_sign_up', apply_filters('ifthengive_register_post_type_sign_up', array(
                    'labels' => array(
                        'name' => __('IfThenGive Sign up', 'ifthengive'),
                        'singular_name' => __('IfThenGive Sign up', 'ifthengive'),
                        'menu_name' => _x('IfThenGive Sign up', 'Admin menu name', 'ifthengive'),                        
                        'edit' => __('Edit', 'ifthengive'),
                        'edit_item' => __('View IfThenGive Sign up', 'ifthengive'),
                        'new_item' => __('New IfThenGive Sign up', 'ifthengive'),
                        'view' => __('View IfThenGive Sign up', 'ifthengive'),
                        'view_item' => __('View IfThenGive Sign up', 'ifthengive'),
                        'search_items' => __('Search IfThenGive Sign up', 'ifthengive'),
                        'not_found' => __('No users found', 'ifthengive'),
                        'not_found_in_trash' => __('No users found in trash', 'ifthengive'),
                        'parent' => __('Parent IfThenGive Sign up', 'ifthengive')
                    ),
                    'description' => __('This is where you can create new IfThenGive Sign up.', 'ifthengive'),
                    'public' => false,
                    'show_ui' => false,
                    'capability_type' => 'post',                    
                    'map_meta_cap' => true,
                    'publicly_queryable' => false,
                    'exclude_from_search' => true,
                    'hierarchical' => false, // Hierarchical causes memory issues - WP loads all records!
                    'rewrite' => array('slug' => 'itg_sign_up'),
                    'query_var' => false,
                    'menu_icon' => 'dashicons-editor-table',
                    'supports' => array('title'),
                    'has_archive' => true,
                    'show_in_nav_menus' => false
                        )
                )
        );
    }
    
}

AngellEYE_IfThenGive_Post_type_Sign_Up::init();