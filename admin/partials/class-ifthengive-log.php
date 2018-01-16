<?php

/**
 * This class defines all code necessary logs
 * @class       AngellEYE_IfThenGive_Log
 * @version	1.0.0
 * @package	IfThenGive/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Log {

    public static function init() {
        add_action('ifthengive_logs_setting', array(__CLASS__, 'ifthengive_logs_setting'));
    }

    public static function ifthengive_logs_setting() {

        $logs = self::scan_log_files(ITG_LOG_DIR);        
        $directory_name = 'connect_to_PayPal';
        if (!empty($_REQUEST['log_file'])) {
            $directory = explode('|',$_REQUEST['log_file']);
            $directory_name = $directory[1];
            $viewed_log = current($logs[$directory_name]);            
        } elseif( !empty($logs['connect_to_PayPal'])  || !empty ($logs['express_checkout']) || !empty ($logs['transactions'])) {
            $viewed_log = current($logs[$directory_name]);
        }
        
        if (!empty($logs['connect_to_PayPal'])  || !empty ($logs['express_checkout']) || !empty ($logs['transactions'])  ) :
            ?>
           <div class="wrap">
            <div id="log-viewer-select">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-info"><?php printf(__('Log file: %s', ITG_TEXT_DOMAIN), str_replace('_',' ',ucwords($directory_name,'_'))); ?></h4>
                        </div>    
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                      <div class="col-md-12">                        
                        <form action="<?php echo admin_url('admin.php?page=ifthengive_option&tab=logs'); ?>" method="post">
                            <select name="log_file">
                                <?php
                                foreach ($logs as $log_key => $log_file) :
                                    foreach ($log_file as $file_key => $file_value) :
                                        ?>
                                <option value="<?php echo esc_attr($file_key.'|'.$log_key); ?>" <?php selected(sanitize_title($viewed_log), $file_key); ?>><?php echo esc_html(str_replace('_',' ',ucwords($log_key,'_'))); ?></option>
                                        <?php
                                    endforeach;
                                endforeach;
                                ?>
                            </select>
                            <input type="submit" class="button" value="<?php esc_attr_e('View', ITG_TEXT_DOMAIN); ?>" />
                        </form>
                      </div>                        
                    </div>
                    <div class="clearfix"></div>                    
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="log-viewer">
                                <?php                                                                                                
                                if ($viewed_log === false) {
                                    $content = __('There are currently no logs to view.', ITG_TEXT_DOMAIN);
                                }
                                else{
                                    $content = file_get_contents(ITG_LOG_DIR .'/'.$directory_name.'/'. $viewed_log);
                                }
                                ?>
                                <textarea readonly="true" rows="25" class="form-control"><?php echo esc_textarea($content); ?></textarea>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
            </div>                        
        <?php else : ?>
            <div class="updated woocommerce-message inline"><p><?php _e('There are currently no logs to view.', ITG_TEXT_DOMAIN); ?></p></div>
        <?php
        endif;
    }

    public static function scan_log_files($dir) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", "..", ".htaccess", "index.html"))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::scan_log_files($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[sanitize_title($value)] = $value;
                }
            }
        }
        return $result;
    }

}

AngellEYE_IfThenGive_Log::init();
