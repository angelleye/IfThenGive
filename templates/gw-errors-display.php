<?php
/**
 * Give When Error template.
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
if(isset($_SESSION['GW_Error']) && isset($_SESSION['GW_Error_Type'])){
    ?>
<center>
    <h1> <?php _e('Error Type :','angelleye_give_when'); ?> <?php echo $_SESSION['GW_Error_Type']; ?> </h1>
    <p> <?php _e('PayPal Acknowledgement :','angelleye_give_when'); ?> <?php echo $_SESSION['GW_Error_Array']['ACK']; ?></p>
    <p> <?php _e('PayPal Error Code :','angelleye_give_when'); ?> <?php echo $_SESSION['GW_Error_Array']['L_ERRORCODE0']; ?></p>
    <p> <?php _e('PayPal Error Short Message :','angelleye_give_when'); ?> <?php echo $_SESSION['GW_Error_Array']['L_SHORTMESSAGE0']; ?></p>
    <p> <?php _e('PayPal Error Long Message :','angelleye_give_when'); ?> <?php echo $_SESSION['GW_Error_Array']['L_LONGMESSAGE0']; ?></p>
</center>
<?php    
    unset($_SESSION['GW_Error'],$_SESSION['GW_Error_Type'],$_SESSION['GW_Error_Array']);
}
else{
    _e("No Data Found.","angelleye_give_when");
}

get_footer();
?>