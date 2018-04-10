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
                $('#manual_amount_input_div').addClass('hidden');
            }
        });
        $(document).on('click','#manual_radio',function () {
            if ($(this).is(':checked')) {
                $('#manual_amount_input_div').removeClass('hidden');
                $('#fixed_amount_input_div').addClass('hidden');
                $('#dynamic_options').addClass('hidden');                
            }
        });
        
        $(document).on('click','#option_radio',function () {
            if ($(this).is(':checked')) {
                $('#dynamic_options').removeClass('hidden');
                $('#fixed_amount_input_div').addClass('hidden');                
                $('#manual_amount_input_div').addClass('hidden');
            }
        });
                
        var room = jQuery("button.remove_dynamic_fields").length;
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
            divtest.innerHTML = '<div class="col-sm-1 nopadding"><label class="control-label">Option </label></div><div class="col-sm-3 nopadding"><div class="form-group"><input type="text" class="form-control" id="option_name'+room+'" autocomplete="off" name="option_name[]" value="" placeholder="Option Name"></div></div><div class="col-sm-3 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="option_amount'+room+'" autocomplete="off" name="option_amount[]" value="" placeholder="Option Amount"><div class="input-group-btn"> <button class="btn btn-danger remove_dynamic_fields" type="button" data-fieldid="'+room+'"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button></div></div></div></div><div class="clear"></div>';
            objTo.prepend(divtest);            
        });
        
        $(document).on('click','.remove_dynamic_fields',function(){            
           var rid = $(this).attr('data-fieldid');
           $('.removeclass'+rid).remove();
        });
                        
        if(typeof tinymce != 'undefined') {            
            tinymce.PluginManager.add('ifthengive_shortcodes', function( editor )
            {
                var shortcodeValues = [];                
                jQuery.each(itg_shortcodes_button_array.shortcodes_button, function( post_id, post_title )
                {                    
                    shortcodeValues.push({
                        text: post_title, 
                        value: post_id
                    });                  
                    
                });
                editor.addButton('ifthengive_shortcodes', {
                    text: 'IfThenGive',
                    type: 'listbox',
                    title: 'IfThenGive',
                    cmd: 'itg_shortcodes',
                    icon: 'mce-ico mce-i-wp_more',
                    onselect: function() {                           
                        if(this.text()=='Transaction'){
                            tinyMCE.activeEditor.selection.setContent( '[ifthengive_transactions]' );
                        }
                        else if(this.text()=='Account'){
                            tinyMCE.activeEditor.selection.setContent( '[ifthengive_account]' );
                        }
                        else if (this.text() == 'My Signedup Goals'){
                            tinyMCE.activeEditor.selection.setContent( '[ifthengive_goals]' );
                        }
                        else{
                            tinyMCE.activeEditor.selection.setContent( '[ifthengive_goal id=' + this.value() + ']' );
                        }
                    },
                    values: shortcodeValues
                });                                
            });
        }
        
        $(document).on('click','#ifthengive_fun',function(){
            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary";
            alertify.defaults.theme.cancel = "btn btn-danger";
            alertify.defaults.theme.input = "form-control";
            var post_id = $('#ifthengive_fun').attr('data-postid');
            $.ajax({
                type: 'POST',
                url: admin_ajax_url,
                 data: { 
                    action  : 'check_goal_is_in_process',
                    post_id : post_id
                },
                dataType: "json",
                beforeSend: function () {
                    $('#itg_fun_refresh').removeClass('hidden');
                }, 
                success: function (result) {
                    if(result.in_process == 'true'){
                        
                        if(result.same_goal == 'true'){
                            var content = $('#div_goal_in_process').html();
                            alertify.alert('Transactions are in Process','').setContent(content).show();
                            $('#itg_fun_refresh').addClass('hidden');
                        }
                        else{
                            alertify.alert('In Process', 'Other Donation Process are working. You can start when it gets over.!');
                            $('#itg_fun_refresh').addClass('hidden');
                        }
                    }
                    else{
                        alertify.confirm('Process Donation', 'Are you ready to process donations for all Givers on this goal?',
                        function ()
                        {                                        
                            alertify.success('Processing Donations...');
                            $('#itg_fun_refresh').addClass('hidden');
                            window.location.href = $('#ifthengive_fun').attr('data-redirectUrl');
                        },
                        function ()
                        {
                            alertify.error('The donation process has been canceled.');
                            $('#itg_fun_refresh').addClass('hidden');
                        });
                    }
                }
            });
            return false;                                   
        });
        
        $(document).on('click','#ifthengive_fun_retry',function(){
            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary";
            alertify.defaults.theme.cancel = "btn btn-danger";
            alertify.defaults.theme.input = "form-control";
            var post_id = $('#ifthengive_fun_retry').attr('data-postid');
            $.ajax({
                type: 'POST',
                url: admin_ajax_url,
                 data: { 
                    action  : 'check_goal_is_in_process',
                    post_id : post_id
                },
                dataType: "json",
                success: function (result) {
                    if(result.in_process == 'true'){
                        
                        if(result.same_goal == 'true'){
                            var content = $('#div_goal_in_process').html();
                            alertify.alert('Transactions are in Process','').setContent(content).show();
                        }
                        else{
                            alertify.alert('In Process', 'Other Donation Process are working. You can start when it gets over.!');
                        }
                    }
                    else{
                        alertify.confirm('Process Donation', 'Are you ready to process donations for all Givers on this goal?',
                        function ()
                        {                                        
                            alertify.success('Processing Donations...');
                            window.location.href = $('#ifthengive_fun_retry').attr('data-redirectUrl');
                        },
                        function ()
                        {
                            alertify.error('The donation process has been canceled.');
                        });
                    }
                }
            });
            return false;                                   
        });
        
        $(document).on('click','#itg_sandbox_enable',function(){
            var sandbox = '';            
            if ($(this).is(':checked')){
                sandbox = true;         
                $('#ifthengive_sandbox_fields').show();
                $('#ifthengive_live_fields').hide();
            } else { 
                sandbox = false;
                $('#ifthengive_sandbox_fields').hide();
                $('#ifthengive_live_fields').show();
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
        $("#itgsandboxClass").on('show.bs.collapse', function(){
            $("#itgsandbox_details").text('').text('Hide Advanced Details');
        });
        $("#itgsandboxClass").on('hide.bs.collapse', function(){
            $("#itgsandbox_details").text('').text('Show Advanced Details');
        });
        
        $("#itgliveClass").on('show.bs.collapse', function(){
            $("#itglive_details").text('').text('Hide Advanced Details');
        });
        $("#itgliveClass").on('hide.bs.collapse', function(){
            $("#itglive_details").text('').text('Show Advanced Details');
        });
        $('#fixed_amount_input').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        $(document).on('input','input[name="option_amount[]"]', function() {
           this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); 
        });
        $('#itg_manual_amount_input').on('input', function() {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });
        
        $( "input[name='post_title']" ).focusout(function() {
            $('input[name="trigger_thing"]').focus();    
        });
        $( ".btn-preview" ).focusout(function() {
            $('#save-post').focus();
        });
        
        $(document).on('click','.btn-cbaid',function(){
            var userid = $(this).data('userid');
            var status = $(this).data('itgchangestatus');
            var post_id = $(this).data('postid');
            var btn = $(this);
            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary";
            alertify.defaults.theme.cancel = "btn btn-danger";
            alertify.defaults.theme.input = "form-control";
            alertify.confirm(status + ' this Giver?', 'Are you sure you want to '+status+' this Giver?',
                function ()
                {                                                            
                    $.ajax({
                       type: 'POST',
                       url: admin_ajax_url,
                        data: { 
                           action  : 'cancel_billing_agreement_giver',
                           userid : userid,
                           postid : post_id
                       },
                       dataType: "json",
                       success: function (result) {                           
                           if(btn.hasClass('btn-warning')){
                               btn.removeClass('btn-warning');
                               btn.addClass('btn-defalt');
                               btn.text('Activate');
                               btn.closest('tr').addClass('itg_suspended_row');                                                      
                               btn.data('itgchangestatus','Activate');
                               alertify.error('Giver Suspended');                               
                           }
                           else{
                               btn.removeClass('btn-defalt');
                               btn.addClass('btn-warning');
                               btn.text('Suspend');
                               btn.closest('tr').removeClass('itg_suspended_row');
                               btn.data('itgchangestatus','Suspend');
                               alertify.success('Giver Activated');                               
                           }
                       }
                   });
                },
                function ()
                {
                    //alertify.error('You Pressed Cancel');
                });               
        });
        
        $(document).on('click','#itg_sandbox_add_manually', function(){
            if ($(this).is(':checked')){                
                $("#itg_sb_api_credentials_username").removeAttr('disabled');
                $("#itg_sb_api_credentials_password").removeAttr('disabled');
                $("#itg_sb_api_credentials_signature").removeAttr('disabled');
            }
            else{
                $('#itg_sb_api_credentials_username').attr('disabled','disabled');
                $('#itg_sb_api_credentials_password').attr('disabled','disabled');
                $('#itg_sb_api_credentials_signature').attr('disabled','disabled');
            }
        });
        
        $(document).on('click','#itg_live_add_manually', function(){
            if ($(this).is(':checked')){                
                $("#itg_lv_api_credentials_username").removeAttr('disabled');
                $("#itg_lv_api_credentials_password").removeAttr('disabled');
                $("#itg_lv_api_credentials_signature").removeAttr('disabled');
            }
            else{
                $('#itg_lv_api_credentials_username').attr('disabled','disabled');
                $('#itg_lv_api_credentials_password').attr('disabled','disabled');
                $('#itg_lv_api_credentials_signature').attr('disabled','disabled');
            }
        });
        
        
        	/*
	 * Select/Upload image(s) event
	 */
        $(document).on('click','.upload_image_button ', function(e){
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
                        $('#itg_brandlogo').val(image_url);
                    });
        });    
        
        $(document).on('click','.upload_hd_image_button ', function(e){
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
                        $('#itg_hd_brandlogo').val(image_url);
                    });
        });     
        if($('[data-toggle="tooltip"]').length > 0){
            $('[data-toggle="tooltip"]').tooltip();   
        }
        
        $(document).on('click','.btn-dgiver ', function(e){
            
            var userid =$(this).attr('data-userid');
            var post_id =$(this).attr('data-signupid');
            var goal_id =$(this).attr('data-goalid');
            var btn = $(this);
            btn.find('span').addClass('glyphicon glyphicon-refresh glyphicon-spin');
            btn.closest('tr').addClass('strikeout');      
            
            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-primary";
            alertify.defaults.theme.cancel = "btn btn-danger";
            alertify.defaults.theme.input = "form-control";
            alertify.confirm('Delete this Giver?', 'Are you sure you want to Delete this Giver from Goal?',
                function ()
                {                                                                                                    
                    $.ajax({
                       type: 'POST',
                       url: admin_ajax_url,
                        data: { 
                           action  : 'delete_giver_from_goal',
                           userid : userid,
                           signup_postid : post_id,
                           goal_id : goal_id                           
                       },
                       dataType: "json",
                       success: function (result) {
                           alertify.error('Giver Deleted');
                           window.location.reload();
                       }
                   });
                },
                function ()
                {                    
                    btn.find('span').removeClass('glyphicon glyphicon-refresh glyphicon-spin');
                    btn.closest('tr').removeClass('strikeout'); 
                });             
        });
        
    });    

})(jQuery);
