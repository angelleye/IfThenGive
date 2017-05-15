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
        
        $(document).on('click','#option_radio',function () {
            if ($(this).is(':checked')) {
                $('#fixed_amount_input_div').addClass('hidden');
                $('#dynamic_options').removeClass('hidden');
            }
        });
        
        var room = 1;
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
        
    });
    

})(jQuery);
