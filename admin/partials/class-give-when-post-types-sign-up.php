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
     * @since    1.0.0
     * @access   static
     */
    public static function init() {      
        add_action('init', array(__CLASS__, 'give_when_register_post_type_sign_up'), 5);
    }
    
    /**
     * give_when_register_post_type_sign_up function is user for register custom post type
     * @since    1.0.0
     * @access   public
     */
    public static function give_when_register_post_type_sign_up() {
        global $wpdb;
        if (post_type_exists('itg_sign_up')) {
            return;
        }
        register_post_type('itg_sign_up', apply_filters('give_when_register_post_type_sign_up', array(
                    'labels' => array(
                        'name' => __('IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'singular_name' => __('IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'menu_name' => _x('IfThenGive Sign up', 'Admin menu name', ITG_TEXT_DOMAIN),                        
                        'edit' => __('Edit', ITG_TEXT_DOMAIN),
                        'edit_item' => __('View IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'new_item' => __('New IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'view' => __('View IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'view_item' => __('View IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'search_items' => __('Search IfThenGive Sign up', ITG_TEXT_DOMAIN),
                        'not_found' => __('No users found', ITG_TEXT_DOMAIN),
                        'not_found_in_trash' => __('No users found in trash', ITG_TEXT_DOMAIN),
                        'parent' => __('Parent IfThenGive Sign up', ITG_TEXT_DOMAIN)
                    ),
                    'description' => __('This is where you can create new IfThenGive Sign up.', ITG_TEXT_DOMAIN),
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

AngellEYE_Give_When_Post_type_sign_up::init();