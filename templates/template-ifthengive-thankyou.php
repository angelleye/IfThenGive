<?php
/**
 * IfThenGive Thankyou template.
 *
 * This template can be overriden by copying this file to your-theme/ifthengive/template-ifthengive-thankyou.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     0.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Don't allow direct access
?>

<?php 
get_header();
?>

<div class="itg_center_container" style="display: inline-block;width: 100%;padding-bottom: 75px;">
    <div class="itgcontainer">
        <div class="itg_heading itg_heading-center">            
<?php
if(isset($_REQUEST['goal']) && isset($_REQUEST['amt'])){
    $the_slug = sanitize_text_field($_REQUEST['goal']);
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
        ?>
            <h2><?php echo sprintf('%1$s %2$s %3$s %4$s',
                    esc_html__('Hi','ifthengive'),
                    $user->display_name,
                    esc_html__(', Thank you for giving to ','ifthengive'),
                    esc_html__($trigger_name,'ifthengive')
                );
            ?></h2>
            <h3><?php
                    echo sprintf('%1$s %2$s %3$s %4$s %5$s',
                        esc_html__('If', 'ifthengive'),
                        esc_html__($trigger_thing, 'ifthengive'),
                        esc_html__('Then Give', 'ifthengive'),                        
                        $symbol,
                        $amount
                    );
            ?></h3>
            <div class="itg_post-image" style="margin-top:30px;max-width: 600px;margin-left: auto;margin-right: auto;">
                <img src="<?php esc_attr_e($image_url);?>" alt="Goal Image">
            </div>
            <div class="itg_post-description" style="max-width: 600px;margin-left: auto;margin-right: auto;">
                <p><?php esc_html_e($trigger_desc,'ifthengive'); ?></p>
            </div>
            <?php          
        $args = array(
            'display_name' => $current_user->display_name,
            'trigger_name' => $trigger_name,
            'symbol'       => $symbol,
            'amount'       => $amount,
            'trigger_thing' => $trigger_thing
        );
        ob_start();
        Ifthengive_Public::itg_get_template('thankyou-email', $args, 'ifthengive/email/', '/templates/email/');              
        $email_data = ob_get_clean();
        
        $headers = "From: IfThenGive <info@ifthengive.com> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $to = $current_user->user_email;
        $subject = __($trigger_name,'ifthengive');
        $message = $email_data;
        wp_mail($to, $subject, $message, $headers);
    }
}
else{
    ?>
            <h3><?php echo __("You are accessing this page without signed up for Goal",'ifthengive'); ?></h3>
            <span><?php echo __("Try again Sigining in for Goals.",'ifthengive'); ?></span>
<?php
    }
?>
         </div>
    </div>
</div>
<?php
get_footer();