<?php
/**
 * IfThenGive Thankyou template.
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
      'post_type'   => 'ifthengive_goals',
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
        $image_url= $post_meta_array['image_url'][0];
        $trigger_desc = $post_meta_array['trigger_desc'][0];
        $ccode = get_option('itg_currency_code');
        $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
        $symbol = $paypal->get_currency_symbol($ccode);
        echo "<h2>".__('Hi ',ITG_TEXT_DOMAIN). $user->display_name . __(', Thank You for signed up in ',ITG_TEXT_DOMAIN) . __($trigger_name,ITG_TEXT_DOMAIN). "</h2>";
        echo "<span>" . __('Each time you will give ',ITG_TEXT_DOMAIN).$symbol.$amount.' '. __('when',ITG_TEXT_DOMAIN).' '.__($trigger_thing,ITG_TEXT_DOMAIN). "</span>";
        echo '<div class="gw_post-image" style="margin-top: 30px;margin-top: 30px;max-width: 600px;margin-left: auto;margin-right: auto;">
                <img src="'.$image_url.'" alt="Goal Image">
              </div>
              <div class="gw_post-description" style="    max-width: 600px;margin-left: auto;margin-right: auto;">
                <p>'.__($trigger_desc,ITG_TEXT_DOMAIN).'</p>
              </div>';
        $EmailString='';
        $EmailString .= '<div style="margin-right: -15px; margin-left: -15px;">
            <div style="width: 100%;">                
                <div style="width: 100%; margin: 10px auto 25px; float: none; height: auto; color: #f58634; font-weight: 600; text-align: center;">
                    <strong style="line-height: 25px;padding: 10px 10px 10px 10px;font-weight: 300; letter-spacing: 1px;text-transform: uppercase; margin-bottom:10px; font-size: 15px;">'. __('Hi '.$current_user->display_name.',Thank You for signed up in '.$trigger_name,ITG_TEXT_DOMAIN).'</strong>
                    <p style="padding: 10px 10px 10px 10px;font-size: 12px;text-align: center;font-family: inherit; color: #076799"><strong>'.__('Each time you will give '.$symbol.$amount.' when '.$trigger_thing,ITG_TEXT_DOMAIN).'</strong></p>      
                </div>
            </div>
        </div>';        

        
        $EmailHeader = '<div dir="ltr" style="background-color: rgb(245, 245, 245); margin: 0; padding: 70px 0 70px 0; width: 100%; height:100%">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" min-height="100%">
                                <tbody>
                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: rgb(253, 253, 253); border: 1px solid rgb(220, 220, 220)">
                                                <tbody>
                                                    <tr>
                                                        <td align="center" valign="top">
                                                            <table border="0" cellpadding="0" cellspacing="0" width="600" style=" color: rgb(255, 255, 255); border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="padding: 10px; display: block">
                                                                          <h1 style="color: rgb(255, 255, 255); font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: center; text-shadow: 0 1px 0 rgb(119, 151, 180)"><img src="'.ITG_PLUGIN_URL.'/admin/images/givewhen.png" alt="IfThenGive"></h1> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    <td align="center" valign="top">';
        
        
        $EmailFooter = '</td></tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    </div>';
        
        
        $headers = "From: IfThenGive <info@ifthengive.com> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $to = $current_user->user_email;
        $subject = __('Thank you for giving '.$symbol.$amount.' For '.$trigger_name,ITG_TEXT_DOMAIN);
        $message = $EmailHeader .$EmailString . $EmailFooter;
        wp_mail($to, $subject, $message, $headers);
    }
}
else{
    ?>
            <h3><?php echo __("You are accessing this page without signed up for Goal",ITG_TEXT_DOMAIN); ?></h3>
            <span><?php echo __("Try again Sigining in for Goals.",ITG_TEXT_DOMAIN); ?></span>
<?php
    }
?>
         </div>
    </div>
</div>
<?php
get_footer();