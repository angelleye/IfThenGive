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
                $html .= '<div class="container">';
                $html .= '<div class="row">';
                $html .= '<h2>View Event</h2>';
                $html .= '<div class="col-md-6">';
                $html .= '<img src="'.get_post_meta( $post->ID, 'image_url', true ).'" height="200px" width="200px">';
                $html .= $post->post_content;
                $html .= '</div>';
                $html .= '<div class="col-md-6">';
                $html .= '<h2>'.get_post_meta( $post->ID, 'trigger_name', true ).'</h2>';
                $html .= '<p>'.get_post_meta( $post->ID, 'trigger_desc', true ).'</p>';
                $amount = get_post_meta($post->ID,'amount',true);
                if($amount == 'fixed'){
                    $html .= get_post_meta($post->ID,'fixed_amount_input',true);
                }else{
                    $option_name = get_post_meta($post->ID,'option_name',true);
                    $option_amount = get_post_meta($post->ID,'option_amount',true);
                    $i=0;
                    //$html .= '<table><tr><td>#</td><td>Name</td><td>Value</td></tr>';
                    $html .= '<div class="form-group">';
                    $html .= '<select class="form-control" name="option_amount">';
                    foreach ($option_name as $name) {
                        $html .= '<option value="'.$option_amount[$i].'">'.$name.'</option>';
                        $i++;
                    }
                    $html .= '</select>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= $post->post_title;
                
                return $html;
                
            }
        }
    }

}

AngellEYE_Give_When_Public_Display::init();
