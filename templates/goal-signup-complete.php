<?php
/**
 * GiveWhen Thankyou template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/goal-signup-complete.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
?>

<?php 
get_header();
?>

<div class="gw_center_container">
    <div class="gwcontainer">
        <div class="gw_heading gw_heading-center">            
<?php
if(isset($_REQUEST['goal']) && isset($_REQUEST['amt'])){
    $the_slug = $_REQUEST['goal'];
    $amount = base64_decode($_REQUEST['amt']);
    $args = array(
      'name'        => $the_slug,
      'post_type'   => 'give_when_goals',
      'post_status' => 'publish',
      'numberposts' => 1
    );
    $my_posts = get_posts($args);
    if( $my_posts ) {
        $user_id = base64_decode($_REQUEST['user']);
        $user = get_user_by('id', $user_id);        
        $post_id = $my_posts[0]->ID;
        $post_meta_array = get_post_meta($post_id);
        $trigger_name = $post_meta_array['trigger_name'][0];
        $trigger_thing = $post_meta_array['trigger_thing'][0];
        $ccode = get_option('gw_currency_code');
        $paypal = new Give_When_PayPal_Helper();
        $symbol = $paypal->get_currency_symbol($ccode);
        echo "<h2>".__('Hi ','givewhen'). $user->display_name . __(', Thank You for signed up in ','givewhen') . $trigger_name. "</h2>";
        echo "<span>" . __('Each time you will give ','givewhen').$symbol.$amount.' '. __('when','givewhen').' '.$trigger_thing. "</span>";
        $EmailString='';
        $EmailString .= '<div style="margin-right: -15px; margin-left: -15px;">
            <div style="width: 100%;">
                <div style="text-align: center;"><img style="vertical-align:margin:0 auto; middle;" src="'.GW_PLUGIN_URL.'admin\images\icon.png" alt="GiveWhen"></div>
                <div style="width: 100%; margin: 10px auto 25px; float: none; height: auto; color: #f58634; font-weight: 600; text-align: center;">
                    <strong style="background-color: #ffffff; font-weight: 300; letter-spacing: 1px;text-transform: uppercase; margin-bottom:10px; font-size: 25px;">Hi '.$current_user->display_name.',Thank You for signed up in Anthony Rizzo Home Run Challenge'.$trigger_name.'</strong>
                    <p style="font-size: 16px;text-align: center;font-family: inherit; color: #076799"><strong>Each time you will give $ '.$amount.' when '.$trigger_thing.'</strong></p>      
                </div>
            </div>
        </div>';        

        $headers = "From: GiveWhen <info@givewhen.com> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $to = $current_user->user_email;
        $subject = 'Thank you for giving!';
        $message = $EmailString;
        wp_mail($to, $subject, $message, $headers);
    }
}
else{
    ?>
            <h3><?php echo __("You are accessing this page without signed up for GiveWhen Goal",'givewhen'); ?></h3>
            <span><?php echo __("Try again Sigining in for GiveWhen Goals.",'givewhen'); ?></span>
<?php
    }
?>
         </div>
    </div>
</div>
<?php
get_footer();