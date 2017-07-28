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
        if (post_type_exists('give_when_sign_up')) {
            return;
        }

        do_action('give_when_register_post_type_sign_up');

        register_post_type('give_when_sign_up', apply_filters('give_when_register_post_type_sign_up', array(
                    'labels' => array(
                        'name' => __('GiveWhen Sign up', 'angelleye_give_when'),
                        'singular_name' => __('GiveWhen Sign up', 'angelleye_give_when'),
                        'menu_name' => _x('GiveWhen Sign up', 'Admin menu name', 'angelleye_give_when'),                        
                        'edit' => __('Edit', 'angelleye_give_when'),
                        'edit_item' => __('View GiveWhen Sign up', 'angelleye_give_when'),
                        'new_item' => __('New GiveWhen Sign up', 'angelleye_give_when'),
                        'view' => __('View GiveWhen Sign up', 'angelleye_give_when'),
                        'view_item' => __('View GiveWhen Sign up', 'angelleye_give_when'),
                        'search_items' => __('Search GiveWhen Sign up', 'angelleye_give_when'),
                        'not_found' => __('No users found', 'angelleye_give_when'),
                        'not_found_in_trash' => __('No users found in trash', 'angelleye_give_when'),
                        'parent' => __('Parent GiveWhen Sign up', 'angelleye_give_when')
                    ),
                    'description' => __('This is where you can create new GiveWhen Sign up.', 'angelleye_give_when'),
                    'public' => false,
                    'show_ui' => false,
                    'capability_type' => 'post',                    
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
    
}

AngellEYE_Give_When_Post_type_sign_up::init();