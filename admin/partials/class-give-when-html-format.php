<?php

/**
 * This class defines all code necessary to generate interface
 * @class       AngellEYE_Give_When_interface
 * @version	1.0.0
 * @package	give-when/partials
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_interface { 
    /**
     * Hook in methods
     * @since    0.1.0
     * @access   static
     */
    public static function init() {        
        add_action('give_when_interface', array(__CLASS__, 'give_when_interface_html'));          
    }
    
    /**
     * give_when_interface_html function is for
     * html of interface.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_interface_html() {        
        global $post;           
        $trigger_name = !empty(get_post_meta($post->ID,'trigger_name',true)) ? get_post_meta($post->ID,'trigger_name',true) : '';
        $trigger_desc = !empty(get_post_meta($post->ID,'trigger_desc',true)) ? get_post_meta($post->ID,'trigger_desc',true) : '';
        $image_url    = !empty(get_post_meta($post->ID,'image_url',true)) ? get_post_meta($post->ID,'image_url',true) : '';        
        $gw_amount = get_post_meta($post->ID,'amount',true);
        if($gw_amount == 'fixed'){
            $fixed_amount_input_class = "";
            $fixed_amount_input_value = !empty(get_post_meta($post->ID,'fixed_amount_input',true)) ? get_post_meta($post->ID,'fixed_amount_input',true) : '';
        }
        else{
            $fixed_amount_input_class = "hidden";
            $fixed_amount_input_value = '';
        }
        
        if($gw_amount == 'select'){
            $dynamic_options_class = '';
            $dynamic_options_name = !empty(get_post_meta($post->ID,'option_name',true)) ? get_post_meta($post->ID,'option_name',true) : '';
            $dynamic_option_amount = !empty(get_post_meta($post->ID,'option_amount',true)) ? get_post_meta($post->ID,'option_amount',true) : '';
        }
?>
        
        <div style="padding-top: 25px"></div>
        <div class="container" style="max-width: 100%">   
            <form>
                <div class="form-group">
                    <label for="triggerName" class="control-label">Trigger Name</label>
                    <input type="text" name="trigger_name" value="<?php echo $trigger_name?>" class="form-control" autocomplete="off" id="trigger_name" placeholder="Enter Name Here"/>
                </div>
                <div class="form-group">
                    <label for="triggerDesc" class="control-label">Trigger Description</label>
                    <textarea name="trigger_desc" class="form-control" autocomplete="off" id="trigger_desc" placeholder="Enter Description Here"><?php echo $trigger_desc;?></textarea>
                </div>
                <div class="form-group">
                    <label for="image_url">Image</label>
                    <input type="text" name="image_url" id="image_url" class="form-control" value="<?php echo $image_url; ?>"><br>
                    <input type="button" name="upload-btn" id="upload-btn" class="btn btn-primary" value="Upload Image">
                </div>                
                <div class="form-group">
                    <input type="radio" name="fixed_radio" id="fixed_radio" value="fixed" checked="checked"><label class="radio-inline" for="fixed_radio"><strong>Fixed</strong></label>
                     &nbsp;<input type="radio" name="fixed_radio" id="option_radio" value="select"><label class="radio-inline" for="option_radio"><strong>Select</strong></label>
                </div>                
                
                <div class="form-group <?php echo $fixed_amount_input_class; ?>" id="fixed_amount_input_div">
                    <label for="triggerName" class="control-label">Fixed Amount</label>
                    <input type="text" name="fixed_amount_input" value="<?php echo $fixed_amount_input; ?>" class="form-control" autocomplete="off" id="fixed_amount_input" placeholder="Enter Amount"/>
                </div>
                <div id="dynamic_options" class="hidden">
                    <div id="education_fields"></div>
                    <div class="col-sm-1 nopadding">
                        <label class="control-label">Option </label>
                    </div>
                    <div class="col-sm-3 nopadding">
                        <div class="form-group">
                            <input type="text" class="form-control" id="option_name" name="option_name[]" value="" placeholder="Option Name">
                        </div>
                    </div>                
                    <div class="col-sm-3 nopadding">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="option_amount" name="option_amount[]" value="" placeholder="Option Amount">
                                <div class="input-group-btn">
                                    <button class="btn btn-success" type="button" id="add_dynamic_field"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>    
            </form>
        </div>
<?php
    }    
}
AngellEYE_Give_When_interface::init();