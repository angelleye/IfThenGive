<?php
/**
 * Give When Thankyou template.
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
        $post_id = $my_posts[0]->ID;
        $post_meta_array = get_post_meta($post_id);
        $trigger_name = $post_meta_array['trigger_name'][0];
        $trigger_thing = $post_meta_array['trigger_thing'][0];
        echo "<center><h1>Thank You for signed up in {$trigger_name}. </h1></center>";
        echo "<center><h2>Each time you will give $ {$amount} when {$trigger_thing}</h2></center>";
    }
}
else{
    echo "No data found in URL";
}

get_footer();
?>