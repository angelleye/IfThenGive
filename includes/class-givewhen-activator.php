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
            if (!get_option('sandbox_enable_give_when')) {
            	add_option('sandbox_enable_give_when', 'no');
			}
			self::create_files();
            $new_page_title = 'GiveWhenThankyou';
            $new_page_content = '[givewhen_thankyou]';
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
        
        /**
	 * Create files/directories
	 */
	public static function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();
		$files = array(
			array(
				'base' => GW_LOG_DIR,
				'file' => '.htaccess',
				'content' => 'deny from all',
			),
			array(
				'base' => GW_LOG_DIR,
				'file' => 'index.html',
				'content' => '',
			),
			array(
				'base' => GW_LOG_DIR . '/connect_to_paypal',
				'file' => 'index.html',
				'content' => '',
			),
			array(
				'base' => GW_LOG_DIR . '/transactions',
				'file' => 'index.html',
				'content' => '',
			),
			array(
				'base' => GW_LOG_DIR . '/express_checkout',
				'file' => 'index.html',
				'content' => '',
			),
		);

		foreach ($files as $file) {
			if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
				if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
					fwrite($file_handle, $file['content']);
					fclose($file_handle);
				}
			}
		}
	}

}
