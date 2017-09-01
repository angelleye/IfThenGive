<?php
/**
 * GiveWhen My Account template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/gw-errors-display.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
if(! is_admin()){
?>

<?php
get_header(); 
?>
<div class="gw_center_container">
    <div class="gwcontainer">
        <div class="gw_heading gw_heading-center">
            <h3><?php _e("My Account","givewhen"); ?></h3>
            <span><?php echo __("This is myaccount page ",'givewhen'); ?></span>
        </div>
    </div>
</div>
<?php
get_footer();
}
?>