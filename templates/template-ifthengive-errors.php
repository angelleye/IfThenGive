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
        <div class="itg_heading itg_heading-center" style="padding-top: 10px;padding-bottom: 10px">
<?php
if(isset($_SESSION['ITG_Error']) && isset($_SESSION['ITG_Error_Type'])){
    ?>

        <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Errors', 'ifthengive') ?></abbr></div>
    
        <h3> <?php _e('Error Type :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Type']) ? __($_SESSION['ITG_Error_Type'],'ifthengive') : ''; ?> </h3>
        <span> <?php _e('Acknowledgement :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['ACK']) ? __($_SESSION['ITG_Error_Array']['ACK'],'ifthengive') : ''; ?></span>
        <span> <?php _e('Error Code :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_ERRORCODE0']) ? __($_SESSION['ITG_Error_Array']['L_ERRORCODE0'],'ifthengive') : ''; ?></span>
        <span> <?php _e('Error Short Message :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_SHORTMESSAGE0']) ? __($_SESSION['ITG_Error_Array']['L_SHORTMESSAGE0'],'ifthengive') : ''; ?></span>
        <span> <?php _e('Error Long Message :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_LONGMESSAGE0']) ? __($_SESSION['ITG_Error_Array']['L_LONGMESSAGE0'],'ifthengive') : ''; ?></span>
<?php    
    unset($_SESSION['ITG_Error'],$_SESSION['ITG_Error_Type'],$_SESSION['ITG_Error_Array']);
}
else{
    ?> <h3><?php _e("You are accessing this page without any process of Goals.",'ifthengive'); ?></h3>
        <span><?php echo __("Try again Sigining in for Goals.",'ifthengive'); ?></span>
        <?php 
}
?>
    </div>
    </div>
</div>
<?php
get_footer();
?>