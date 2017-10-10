<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link        http://www.angelleye.com/
 * @since       1.0.0
 * @class       AngellEYE_IfThenGive_Admin_Display
 * @package     IfThenGive
 * @subpackage  IfThenGive/admin/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Admin_Display {

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
        add_options_page('IfThenGive', 'IfThenGive', 'manage_options', 'ifthengive_option', array(__CLASS__, 'ifthengive_option'));
    }

    /**
     * ifthengive_option helper will trigger hook and handle all the settings section 
     * @since    0.1.0
     * @access   public
     */
    public static function ifthengive_option() {
        $setting_tabs = apply_filters('ifthengive_setting_tab', array('connect_to_paypal' => 'Connect To PayPal', 'logs' => 'Logs'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'connect_to_paypal';
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs as $name => $label)
                echo '<a href="' . admin_url('admin.php?page=ifthengive_option&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . __($label,ITG_TEXT_DOMAIN) . '</a>';
            ?>
        </h2>
        <?php
        foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
            switch ($setting_tabkey) {
                case $current_tab:
                    do_action('ifthengive_' . $setting_tabkey . '_setting_save_field');
                    do_action('ifthengive_' . $setting_tabkey . '_setting');
                    do_action('ifthengive_' . $setting_tabkey . '_create_setting');
                    break;
            }
        }
    }

}

AngellEYE_IfThenGive_Admin_Display::init();
