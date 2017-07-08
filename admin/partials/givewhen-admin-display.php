<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 * @class      AngellEYE_Give_When_Admin_Display
 * @package    Givewhen
 * @subpackage Givewhen/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_Admin_Display {

    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
    }

    /**
     * add_settings_menu helper function used for add menu for pluging setting
     * @since    0.1.0
     * @access   public
     */
    public static function add_settings_menu() {
        add_options_page('Give When', 'Give When', 'manage_options', 'give_when_option', array(__CLASS__, 'give_when_options'));
    }

    /**
     * paypal_wp_button_manager_options helper will trigger hook and handle all the settings section 
     * @since    0.1.0
     * @access   public
     */
    public static function give_when_options() {
        $setting_tabs = apply_filters('give_when_setting_tab', array('general' => 'PayPal Setup', 'logs' => 'Logs'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs as $name => $label)
                echo '<a href="' . admin_url('admin.php?page=give_when_option&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
        </h2>
        <?php
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('give_when_' . $setting_tabkey . '_setting_save_field');
                    do_action('give_when_' . $setting_tabkey . '_setting');
                    do_action('give_when_' . $setting_tabkey . '_create_setting');
                    break;
            }
        }
    }

}

AngellEYE_Give_When_Admin_Display::init();
