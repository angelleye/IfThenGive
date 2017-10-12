<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    IfThenGive
 * @subpackage IfThenGive/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    IfThenGive
 * @subpackage IfThenGive/includes
 * @author     Angell EYE <andrew@angelleye.com>
 */
class IfThenGive_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        if (!get_option('itg_sandbox_enable')) {
            add_option('itg_sandbox_enable', 'no');
        }
        self::create_files();        
        
        if (!get_option('itg_currency_code ')) {
            add_option('itg_currency_code ', 'USD');
        }
        /* set_transient added for endpoints. */
        set_transient( 'itg_flush', 1, 60 );
    } 

    /**
     * Create files/directories
     */
    public static function create_files() {
        // Install files and folders for uploading files and prevent hotlinking
        $upload_dir = wp_upload_dir();
        $files = array(
            array(
                'base' => ITG_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all',
            ),
            array(
                'base' => ITG_LOG_DIR,
                'file' => 'index.html',
                'content' => '',
            ),
            array(
                'base' => ITG_LOG_DIR . '/connect_to_PayPal',
                'file' => 'index.html',
                'content' => '',
            ),
            array(
                'base' => ITG_LOG_DIR . '/transactions',
                'file' => 'index.html',
                'content' => '',
            ),
            array(
                'base' => ITG_LOG_DIR . '/express_checkout',
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
