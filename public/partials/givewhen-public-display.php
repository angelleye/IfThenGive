<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.angelleye.com/
 * @since      1.0.0
 *
 * @package    Givewhen
 * @subpackage Givewhen/public/partials
 */
class AngellEYE_Give_When_Public_Display {

    public static function init() {
        add_shortcode('give_when', array(__CLASS__, 'give_when_create_shortcode'));
        add_action( 'wp_enqueue_scripts', array(__CLASS__,'give_when_detect_shortcode'));
    }

    public static function give_when_detect_shortcode()
    {
        global $post;
        $pattern = get_shortcode_regex();

        if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'give_when', $matches[2] ) )
        {            
            wp_enqueue_style( 'givewhen-one', GW_PLUGIN_URL . 'includes/css/bootstrap/css/bootstrap.css', array(), '1.0.0','all' );
        }
    }
    
    /**
     * give_when_create_shortcode function is for generate
     * @since 1.0.0
     * @access public
     */
    public static function give_when_create_shortcode($atts, $content = null) {

        global $post, $post_ID;        
        extract(shortcode_atts(array(
                    'id' => ''), $atts));
        $html = '';
        
        if( !empty($id) ) {
            $post = get_post($id);
            if(!empty($post->post_type) && $post->post_type == 'give_when' && $post->post_status == 'publish') {
        ?>
                <div class="give_when_container">
                    <div class="row">
                        <div class="col-md-12"><h1><?php echo get_post_meta( $post->ID, 'trigger_name', true ); ?></h1></div>
                        <div class="col-md-12">
                            <img src="<?php echo get_post_meta( $post->ID, 'image_url', true ) ?>">
                            <br><br>
                            <p> <?php echo get_post_meta( $post->ID, 'trigger_desc', true ); ?></p>
                            <?php echo $post->post_content; ?>
                        </div>
                        <div class="col-md-6">
                            <?php 
                                $amount = get_post_meta($post->ID,'amount',true);
                                if($amount == 'fixed'){
                                    $html .= get_post_meta($post->ID,'fixed_amount_input',true);
                                } else{
                                    $option_name = get_post_meta($post->ID,'option_name',true);
                                    $option_amount = get_post_meta($post->ID,'option_amount',true);
                                    $i=0;
                            ?>
                            <div class="form-group">
                                <select class="form-control" name="option_amount">
                                <?php
                                    foreach ($option_name as $name) {
                                        echo '<option value="'.$option_amount[$i].'">'.$name.$option_amount[$i].'</option>';
                                        $i++;
                                    }
                                ?>
                                </select>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
    }
}

AngellEYE_Give_When_Public_Display::init();
