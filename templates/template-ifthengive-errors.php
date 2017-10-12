<?php
/**
 * IfThenGive Error template.
 *
 * This template can be overriden by copying this file to your-theme/IfThenGive/template-ifthengive-errors.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
?>

<?php 
@session_start();
get_header(); 
?>
<div class="itg_center_container">
    <div class="itgcontainer">
        <div class="itg_heading itg_heading-center">
<?php
if(isset($_SESSION['ITG_Error']) && isset($_SESSION['ITG_Error_Type'])){
    ?>

        <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Errors', ITG_TEXT_DOMAIN) ?></abbr></div>
    
        <h3> <?php _e('Error Type :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['ITG_Error_Type']) ? __($_SESSION['ITG_Error_Type'],ITG_TEXT_DOMAIN) : ''; ?> </h3>
        <span> <?php _e('Acknowledgement :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['ITG_Error_Array']['ACK']) ? __($_SESSION['ITG_Error_Array']['ACK'],ITG_TEXT_DOMAIN) : ''; ?></span>
        <span> <?php _e('Error Code :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_ERRORCODE0']) ? __($_SESSION['ITG_Error_Array']['L_ERRORCODE0'],ITG_TEXT_DOMAIN) : ''; ?></span>
        <span> <?php _e('Error Short Message :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_SHORTMESSAGE0']) ? __($_SESSION['ITG_Error_Array']['L_SHORTMESSAGE0'],ITG_TEXT_DOMAIN) : ''; ?></span>
        <span> <?php _e('Error Long Message :',ITG_TEXT_DOMAIN); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_LONGMESSAGE0']) ? __($_SESSION['ITG_Error_Array']['L_LONGMESSAGE0'],ITG_TEXT_DOMAIN) : ''; ?></span>
<?php    
    unset($_SESSION['ITG_Error'],$_SESSION['ITG_Error_Type'],$_SESSION['ITG_Error_Array']);
}
else{
    ?> <h3><?php _e("You are accessing this page without any process of Goals.",ITG_TEXT_DOMAIN); ?></h3>
        <span><?php echo __("Try again Sigining in for Goals.",ITG_TEXT_DOMAIN); ?></span>
        <?php 
}
?>
    </div>
    </div>
</div>
<?php
get_footer();
?>