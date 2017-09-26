<?php

/**
 * This class defines all code necessary logs
 * @class       AngellEYE_Give_When_Log
 * @version	1.0.0
 * @package	GiveWhen/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_Log {

    public static function init() {
        add_action('give_when_logs_setting', array(__CLASS__, 'give_when_logs_setting'));
    }

    public static function give_when_logs_setting() {

        $logs = self::scan_log_files(GW_LOG_DIR);        
        $directory_name = 'transactions';
        if (!empty($_REQUEST['log_file'])) {
            $directory = explode('|',$_REQUEST['log_file']);
            $directory_name = $directory[1];
            $viewed_log = current($logs[$directory_name]);
        } elseif( !empty($logs['connect_to_paypal'])  || !empty ($logs['express_checkout']) || !empty ($logs['transactions'])) {
            $viewed_log = current($logs[$directory_name]);
        }
        
        if (!empty($logs['connect_to_paypal'])  || !empty ($logs['express_checkout']) || !empty ($logs['transactions'])  ) :
            ?>
           <div class="wrap">
            <div id="log-viewer-select">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-info"><?php printf(__('Log file: %s', 'givewhen'), str_replace('_',' ',ucwords($directory_name,'_'))); ?></h4>
                        </div>    
                    </div>
                    <div class="clearfix"></div>
                    <div class="row">
                      <div class="col-md-12">                        
                        <form action="<?php echo admin_url('admin.php?page=give_when_option&tab=logs'); ?>" method="post">
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
                            <input type="submit" class="button" value="<?php esc_attr_e('View', 'givewhen'); ?>" />
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
                                    $content = __('There are currently no logs to view.', 'givewhen');
                                }
                                else{
                                    $content = file_get_contents(GW_LOG_DIR .'/'.$directory_name.'/'. $viewed_log);
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
            <div class="updated woocommerce-message inline"><p><?php _e('There are currently no logs to view.', 'givewhen'); ?></p></div>
        <?php
        endif;
    }

    /**
     * Scan the log files.
     * @return array
     */
//    public static function scan_log_files() {
//        $files = @scandir(GW_LOG_DIR);
//        $result = array();
//        echo "<pre>";
//        var_dump($files);
//        exit;
//        if (!empty($files)) {
//
//            foreach ($files as $key => $value) {
//
//                if (!in_array($value, array('.', '..'))) {
//                    if (!is_dir($value) && strstr($value, '.log')) {
//                        $result[sanitize_title($value)] = $value;
//                    }
//                }
//            }
//        }
//
//        return $result;
//    }

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

AngellEYE_Give_When_Log::init();
