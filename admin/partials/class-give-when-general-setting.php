<?php

/**
 * This class defines all code necessary to General Setting from admin side
 * @class       AngellEYE_Give_When_General_Setting
 * @version	1.0.0
 * @package    Givewhen
 * @subpackage Givewhen/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_General_Setting {

    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {

        add_action('give_when_general_setting', array(__CLASS__, 'give_when_general_setting'));
        add_action('give_when_general_setting_save_field', array(__CLASS__, 'give_when_general_setting_save_field'));
    }

    /**
     * give_when_general_setting_fields function used for display general setting block from admin side
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_general_setting_fields() {
        $Logger = new AngellEYE_Give_When_Logger();
        $fields[] = array(
            'title' => __('General Settings', 'angelleye_give_when'),
            'type' => 'title',
            'id' => 'general_options_setting'
        );
        $fields[] = array(
            'title' => __('Debug Log', 'angelleye_give_when'),
            'id' => 'log_enable_give_when',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'angelleye_give_when'),
            'default' => 'no',
            'desc' => sprintf(__('Log PayPal WP Button Manager events in <code>%s</code>', 'angelleye_give_when'), $Logger->give_when_for_wordpress_wordpress_get_log_file_path('angelleye_give_when'))
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options_setting');
        return $fields;
    }

    /**
     * give_when_general_setting function used for submit form of settings
     * @since 1.0.0
     * @access public
     */
    public static function give_when_general_setting() {

        $genral_setting_fields = self::give_when_general_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        ?>
       
        <div class="div_general_settings">
            <div class="div_log_settings">
                    <form id="button_manager_integration_form_general" enctype="multipart/form-data" action="" method="post">
                        <?php $Html_output->init($genral_setting_fields); ?>
                        <p class="submit">
                            <input type="submit" name="give_when_intigration" class="button-primary" value="<?php esc_attr_e('Save Settings', 'Option'); ?>" />
                        </p>
                    </form>
            </div>       
        </div>
        <?php
    }
    
    /**
     * give_when_general_setting_save_field function used for save general setting field value
     * @since 1.0.0
     * @access public static
     * 
     */
    public static function give_when_general_setting_save_field() {
        $paypalapi_setting_fields = self::give_when_general_setting_fields();
        $Html_output = new AngellEYE_Give_When_Html_output();
        $Html_output->save_fields($paypalapi_setting_fields);
        if (isset($_POST['give_when_intigration'])):
            ?>
            <div id="setting-error-settings_updated" class="updated settings-error"> 
                <p><?php echo '<strong>' . __('Settings were saved successfully.', 'angelleye_give_when') . '</strong>'; ?></p></div>

            <?php
        endif;
    }

}

AngellEYE_Give_When_General_Setting::init();
