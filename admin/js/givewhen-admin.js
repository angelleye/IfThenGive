(function ($) {
    'use strict';
    jQuery(document).ready(function ($) {
        
        if(give_when_sanbox_enable_js == 'yes'){
            jQuery('#give_when_api_username, #give_when_api_password, #give_when_api_signature').closest('tr').hide();
        }
        else{
            jQuery('#give_when_sandbox_api_username, #give_when_sandbox_api_password, #give_when_sandbox_api_signature').closest('tr').hide();
        }
        
        
        $('#upload-btn').click(function (e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
                    .on('select', function (e) {
                        // This will return the selected image from the Media Uploader, the result is an object
                        var uploaded_image = image.state().get('selection').first();
                        // We convert uploaded_image to a JSON object to make accessing it easier
                        // Output to the console uploaded_image                    
                        var image_url = uploaded_image.toJSON().url;
                        // Let's assign the url value to the input field
                        $('#image_url').val(image_url);
                    });
        });
        
        $(document).on('click','#fixed_radio',function () {
            if ($(this).is(':checked')) {
                $('#fixed_amount_input_div').removeClass('hidden');
                $('#dynamic_options').addClass('hidden');
            }
        });
        
        $(document).on('click','#option_radio',function () {
            if ($(this).is(':checked')) {
                $('#fixed_amount_input_div').addClass('hidden');
                $('#dynamic_options').removeClass('hidden');
            }
        });
                
        var room = jQuery("button#remove_dynamic_fields").length;
        if(room === 0){
            room = 1;
        }
        else{
            room++;
        }
        $('#add_dynamic_field').click(function(){            
             room++;
            var objTo = document.getElementById('education_fields');
            var divtest = document.createElement("div");
                divtest.setAttribute("class", "form-group removeclass"+room);
                var rdiv = 'removeclass'+room;
            divtest.innerHTML = '<div class="col-sm-1 nopadding"><label class="control-label">Option </label></div><div class="col-sm-3 nopadding"><div class="form-group"><input type="text" class="form-control" id="option_name" name="option_name[]" value="" placeholder="Option Name"></div></div><div class="col-sm-3 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="option_amount" name="option_amount[]" value="" placeholder="Option Amount"><div class="input-group-btn"> <button class="btn btn-danger" type="button" id="remove_dynamic_fields" data-fieldid="'+room+'"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button></div></div></div></div><div class="clear"></div>';
            objTo.appendChild(divtest);
        });
        
        $(document).on('click','#remove_dynamic_fields',function(){            
           var rid = $(this).attr('data-fieldid');
           $('.removeclass'+rid).remove();
        });
                        
        if(typeof tinymce != 'undefined') {
            tinymce.PluginManager.add('pushortcodes', function( editor )
            {
                var shortcodeValues = [];
                jQuery.each(shortcodes_button_array.shortcodes_button, function( post_id, post_title )
                {
                    shortcodeValues.push({
                        text: post_title, 
                        value: post_id
                    });

                });
                editor.addButton('pushortcodes', {
                    text: 'Shortcodes',
                    type: 'listbox',
                    title: 'Give When',
                    icon: 'mce-ico mce-i-wp_more',
                    onselect: function() {
                         tinyMCE.activeEditor.selection.setContent( '[give_when_goal id=' + this.value() + ']' );
                    },
                    values: shortcodeValues
                });
            });
        }
        $(document).on('click','#angelleye_connect_to_paypal',function(){            
            $.ajax({
                type: 'POST',
                url: admin_ajax_url,
                 data: { 
                    action: 'request_permission'                    
                },
                dataType: "json",
                beforeSend: function () {
                    $('#overlay').show();
                },                
                success: function (result) {
                   if(result.Ack == 'Success'){
                       window.location.href = result.RedirectURL;
                   }
                   else{
                       $('#overlay').hide();
                       $('#connect_paypal_error').show();
                       $('#connect_paypal_error_p').html('').html('PayPal Acknowledgement : ' + result.Ack);
                       $('#connect_paypal_error_p').append('<br>Message : ' + result.Message);
                       $('#connect_paypal_error_p').append('<br>ErrorID : ' + result.ErrorID);
                       $('#connect_paypal_error_p').append('<br>Please Try Again later');
                   }
                }
            });
        });
        
        $(document).on('click','#give_when_fun',function(){
            if (confirm("Press a button!") == true) {
                window.location.href= '';
            } else {
                return false;
            }
        });
        
        $(document).on('click','#sandbox_enable_give_when',function(){
            var sandbox = '';
            var sandboxTr ='';
            var productionTr = '';
            sandboxTr = jQuery('#give_when_sandbox_api_username, #give_when_sandbox_api_password, #give_when_sandbox_api_signature').closest('tr');
            productionTr = jQuery('#give_when_api_username, #give_when_api_password, #give_when_api_signature').closest('tr');            
            if ($(this).is(':checked')){
                sandbox = true;
                sandboxTr.show();
                productionTr.hide();
            } else { 
                sandbox = false;
                sandboxTr.hide();
                productionTr.show();
            }
             $.ajax({
                type: 'POST',
                url: admin_ajax_url,
                 data: { 
                    action  : 'sandbox_enabled',
                    sandbox : sandbox
                },
                dataType: "json",
                success: function (result) {
                }
            });
        });
        
    });
    

})(jQuery);
