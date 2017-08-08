(function ($) {
    'use strict';
    jQuery(document).ready(function ($) {        
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
        $(document).on('click','#manual_radio',function () {
            if ($(this).is(':checked')) {
                $('#fixed_amount_input_div').addClass('hidden');
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
            console.log('Hey');
            tinymce.PluginManager.add('give_when_shortcodes', function( editor )
            {
                var shortcodeValues = [];
                jQuery.each(gw_shortcodes_button_array.shortcodes_button, function( post_id, post_title )
                {
                    shortcodeValues.push({
                        text: post_title, 
                        value: post_id
                    });

                });
                editor.addButton('give_when_shortcodes', {
                    text: 'GiveWhen',
                    type: 'listbox',
                    title: 'GiveWhen',
                    cmd: 'give_when_shortcodes',
                    icon: 'mce-ico mce-i-wp_more',
                    onselect: function() {
                         tinyMCE.activeEditor.selection.setContent( '[give_when_goal id=' + this.value() + ']' );
                    },
                    values: shortcodeValues
                });
            });
        }
        
        $(document).on('click','#give_when_fun',function(){
            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary";
            alertify.defaults.theme.cancel = "btn btn-danger";
            alertify.defaults.theme.input = "form-control";
            alertify.confirm('Process Donation', 'Are you sure you want to Process Donation..?',
                function ()
                {                                        
                    alertify.success('Process Doantion is Starting.'); 
                    window.location.href = $('#give_when_fun').attr('data-redirectUrl');
                },
                function ()
                {
                    alertify.error('You Pressed Cancel');
                });            
        });
        
        $(document).on('click','#sandbox_enable_give_when',function(){
            var sandbox = '';            
            if ($(this).is(':checked')){
                sandbox = true;         
                $('#give_when_sandbox_fields').show();
                $('#give_when_live_fields').hide();
            } else { 
                sandbox = false;
                $('#give_when_sandbox_fields').hide();
                $('#give_when_live_fields').show();
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
        $("#gwsandboxClass").on('show.bs.collapse', function(){
            $("#gwsandbox_details").text('').text('Hide Advanced Details');
        });
        $("#gwsandboxClass").on('hide.bs.collapse', function(){
            $("#gwsandbox_details").text('').text('Show Advanced Details');
        });
        
        $("#gwliveClass").on('show.bs.collapse', function(){
            $("#gwlive_details").text('').text('Hide Advanced Details');
        });
        $("#gwliveClass").on('hide.bs.collapse', function(){
            $("#gwlive_details").text('').text('Show Advanced Details');
        });
        $('#fixed_amount_input').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $(document).on('input','input[name="option_amount[]"]', function() {
           this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); 
        });
        $('#gw_manual_amount_input').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        
        $('#gw_connect_with_paypal_sb').click(function (){
            $('#overlay').show();
        });
        $('#gw_connect_with_paypal_live').click(function (){
            $('#overlay').show();
        });
    });
    

})(jQuery);
