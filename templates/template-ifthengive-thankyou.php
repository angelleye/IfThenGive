<?php
/**
 * IfThenGive Thankyou template.
 *
 * This template can be overriden by copying this file to your-theme/ifthengive/template-ifthengive-thankyou.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     1.0.0
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
            <img src="<?php echo ITG_PLUGIN_URL.'/admin/images/itg_success.png'; ?>" alt="IfThenGive" class="itg_image_ty_page">
            <h2 class="itg_blue"><?php echo sprintf('%1$s, %2$s <br><br> %3$s <span style="font-weight: 400;">%4$s</span>!',
                    esc_html__('Thank you','ifthengive'),
                    $user->display_name,
                    esc_html__('For giving to','ifthengive'),
                    $trigger_name
                    );
            ?></h2>
            <h3 class="itg_blue"><?php
                    echo sprintf('<span class="itg_ty">%1$s</span> %2$s <span class="itg_ty">%3$s</span> <span class="itg_make_bold">%4$s%5$s</span>',
                        esc_html__('If', 'ifthengive'),
                        $trigger_thing,
                        esc_html__('Then Give', 'ifthengive'),                        
                        $symbol,
                        $amount
                    );
            ?></h3>
            <?php
            if (is_user_logged_in()) {                            
            ?>
            <a class="itg_btn itg_btn-primary ifthengive_angelleye_checkout" href="<?php echo site_url('itg-account'); ?>">
                <?php echo sprintf('%1$s', esc_html__('MANAGE YOUR ACCOUNT','ifthengive')); ?>
            </a>
            <?php 
            }
            ?>
            <?php          
        $args = array(
            'display_name' => $user->display_name,
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

        $to = $user->user_email;
        $subject = __($trigger_name,'ifthengive');
        $message = $email_data;
        wp_mail($to, $subject, $message, $headers);
    }
}
else{
    ?>
            <h3><?php esc_html_e("You are accessing this page without signed up for Goal",'ifthengive'); ?></h3>
            <span><?php esc_html_e("Try again Sigining in for Goals.",'ifthengive'); ?></span>
<?php
    }
?>
         </div>
    </div>
</div>
<?php
get_footer();