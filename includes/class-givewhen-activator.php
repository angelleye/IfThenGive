<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Givewhen
 * @subpackage Givewhen/includes
 * @author     Angell EYE <andrew@angelleye.com>
 */
class Givewhen_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            $new_page_title = 'GiveWhenThankyou';
            $new_page_content = '<div class="container" style="margin-top:5%;">
                                    <div class="row">
                                       <div class="jumbotron">
                                           <h2 class="text-center">Thank you for signing up In GiveWhen</h2>
                                       </div>
                                    </div>
                                 </div>';
            $new_page_template = ''; //ex. template-custom.php. Leave blank if you don't want a custom page template.
            //don't change the code below, unless you know what you're doing
            $page_check = get_page_by_title($new_page_title);
            $new_page = array(
                    'post_type' => 'page',
                    'post_title' => $new_page_title,
                    'post_content' => $new_page_content,
                    'post_status' => 'publish',
                    'post_author' => 1,
            );
            if(!isset($page_check->ID)){
                    $new_page_id = wp_insert_post($new_page);
                    if(!empty($new_page_template)){
                            update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                    }
            }
	}

}
