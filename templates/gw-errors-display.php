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

        <div class="gw_hr-title gw_hr-long gw_center"><abbr><?php _e('GiveWhen Errors', 'givewhen') ?></abbr></div>
    
    <h3> <?php _e('Error Type :','givewhen'); ?> <?php echo $_SESSION['GW_Error_Type']; ?> </h3>
    <span> <?php _e('PayPal Acknowledgement :','givewhen'); ?> <?php echo $_SESSION['GW_Error_Array']['ACK']; ?></span>
    <span> <?php _e('PayPal Error Code :','givewhen'); ?> <?php echo $_SESSION['GW_Error_Array']['L_ERRORCODE0']; ?></span>
    <span> <?php _e('PayPal Error Short Message :','givewhen'); ?> <?php echo $_SESSION['GW_Error_Array']['L_SHORTMESSAGE0']; ?></span>
    <span> <?php _e('PayPal Error Long Message :','givewhen'); ?> <?php echo $_SESSION['GW_Error_Array']['L_LONGMESSAGE0']; ?></span>
<?php    
    unset($_SESSION['GW_Error'],$_SESSION['GW_Error_Type'],$_SESSION['GW_Error_Array']);
}
else{
    ?> <h3><?php _e("No Data Found.","angelleye_give_when"); ?></h3> <?php 
}
?>
    </div>
    </div>
</div>
<?php
get_footer();
?>