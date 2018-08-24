<?php
/**
 * IfThenGive Error template.
 *
 * This template can be overriden by copying this file to your-theme/ifthengive/template-ifthengive-errors.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     0.1.0
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

        <div class="itg_hr-title itg_hr-long itg_center"><abbr><?php esc_html_e('Errors', 'ifthengive') ?></abbr></div>
    
        <h3> <?php esc_html_e('Error Type :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Type']) ? esc_html__($_SESSION['ITG_Error_Type']) : ''; ?> </h3>
        <span> <?php esc_html_e('Acknowledgement :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['ACK']) ? esc_html__($_SESSION['ITG_Error_Array']['ACK']) : ''; ?></span>
        <span> <?php esc_html_e('Error Code :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_ERRORCODE0']) ? esc_html__($_SESSION['ITG_Error_Array']['L_ERRORCODE0']) : ''; ?></span>
        <span> <?php esc_html_e('Error Short Message :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_SHORTMESSAGE0']) ? esc_html__($_SESSION['ITG_Error_Array']['L_SHORTMESSAGE0']) : ''; ?></span>
        <span> <?php esc_html_e('Error Long Message :','ifthengive'); ?> <?php echo isset($_SESSION['ITG_Error_Array']['L_LONGMESSAGE0']) ? esc_html__($_SESSION['ITG_Error_Array']['L_LONGMESSAGE0']) : ''; ?></span>
<?php    
    unset($_SESSION['ITG_Error'],$_SESSION['ITG_Error_Type'],$_SESSION['ITG_Error_Array']);
}
else{
    ?> <h3><?php esc_html_e("You are accessing this page without any process of Goals.",'ifthengive'); ?></h3>
        <span><?php esc_html_e("Try again Sigining in for Goals.",'ifthengive'); ?></span>
        <?php 
}
?>
    </div>
    </div>
</div>
<?php
get_footer();
?>