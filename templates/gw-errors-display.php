<?php
/**
 * GiveWhen Error template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/gw-errors-display.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
?>

<?php 
@session_start();
get_header(); 
?>
<div class="gw_center_container">
    <div class="gwcontainer">
        <div class="gw_heading gw_heading-center">
<?php
if(isset($_SESSION['GW_Error']) && isset($_SESSION['GW_Error_Type'])){
    ?>

        <div class="gw_hr-title gw_hr-long gw_center"><abbr><?php _e('GiveWhen Errors', ITG_TEXT_DOMAIN) ?></abbr></div>
    
        <h3> <?php _e('Error Type :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['GW_Error_Type']) ? __($_SESSION['GW_Error_Type'],ITG_TEXT_DOMAIN) : ''; ?> </h3>
        <span> <?php _e('Acknowledgement :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['GW_Error_Array']['ACK']) ? __($_SESSION['GW_Error_Array']['ACK'],ITG_TEXT_DOMAIN) : ''; ?></span>
        <span> <?php _e('Error Code :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['GW_Error_Array']['L_ERRORCODE0']) ? __($_SESSION['GW_Error_Array']['L_ERRORCODE0'],ITG_TEXT_DOMAIN) : ''; ?></span>
        <span> <?php _e('Error Short Message :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['GW_Error_Array']['L_SHORTMESSAGE0']) ? __($_SESSION['GW_Error_Array']['L_SHORTMESSAGE0'],ITG_TEXT_DOMAIN) : ''; ?></span>
        <span> <?php _e('Error Long Message :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['GW_Error_Array']['L_LONGMESSAGE0']) ? __($_SESSION['GW_Error_Array']['L_LONGMESSAGE0'],ITG_TEXT_DOMAIN) : ''; ?></span>
<?php    
    unset($_SESSION['GW_Error'],$_SESSION['GW_Error_Type'],$_SESSION['GW_Error_Array']);
}
else{
    ?> <h3><?php _e("You are accessing this page without any process of GiveWhen Goals.",ITG_TEXT_DOMAIN); ?></h3>
        <span><?php echo __("Try again Sigining in for GiveWhen Goals.",ITG_TEXT_DOMAIN); ?></span>
        <?php 
}
?>
    </div>
    </div>
</div>
<?php
get_footer();
?>