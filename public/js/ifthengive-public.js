(function( $ ) {
	'use strict';
        jQuery(document).ready(function ($) {
            $(document).on('click','#ifthengive_angelleye_checkout',function(){                
                var amount='';
                var formData = '';
                var user_id = '';
                if ($('#ifthengive_fixed_price_span').length) {
                    amount = $('#ifthengive_fixed_price_span').html();
                }
                if($('#ifthengive_fixed_price_span_select').length){
                    amount = $('#ifthengive_fixed_price_span_select').html();
                }
                if($('#ifthengive_manual_price_span').length){
                    amount = $('#ifthengive_manual_price_span').html();
                }
                
                formData = $("#ifthengive_signup").serialize();                
                user_id = $(this).attr('data-userid');                
                
                $.ajax({
                    type: 'POST',
                    url: admin_ajax_url,
                     data: { 
                        action: 'start_express_checkout',
                        post_id : $(this).attr('data-postid'),
                        amount : amount,
                        formData: formData,
                        login_user_id : user_id
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $('#overlay').show();
                    },                
                    success: function (result) {                      
                       if(result.Ack == 'Success'){
                            $('#ifthengive_signup')[0].reset();
                           window.location.href = result.RedirectURL;
                       }
                       else{
                           if( result.Ack == 'ValidationError'){
                             $('#overlay').hide();
                             $('#connect_paypal_error_public').show();
                             $('#connect_paypal_error_p').html('').html('<strong>Acknowledgement :</strong> ' + result.Ack);
                             $('#connect_paypal_error_p').append('<br><strong>Error Code :</strong> ' + result.ErrorCode);
                             $('#connect_paypal_error_p').append('<br><strong>Long Message :</strong> ' + result.ErrorLong);
                             $('#connect_paypal_error_p').append('<br><strong>Errors :</strong>');
                             $('#connect_paypal_error_p').append('<ul>');
                             jQuery.each( result.Errors , function( i, val ) {                                 
                                 $('#connect_paypal_error_p').append('<li>'+val+'</li>');
                             });           
                             $('#connect_paypal_error_p').append('</ul>');   
                             $('html, body').animate({
                                scrollTop: $("#scrolltopid").offset().top
                            }, 2000);
                           }
                           else{
                            $('#overlay').hide();
                            $('#connect_paypal_error_public').show();
                            $('#connect_paypal_error_p').html('').html('<strong>Acknowledgement :</strong> ' + result.Ack);                           
                            $('#connect_paypal_error_p').append('<br><strong>Error Code :</strong> ' + result.ErrorCode);
                            $('#connect_paypal_error_p').append('<br><strong>Short Message :</strong> ' + result.ErrorShort);
                            $('#connect_paypal_error_p').append('<br><strong>Long Message :</strong> ' + result.ErrorLong);
                            $('#ifthengive_signup')[0].reset(); 
                            $('html, body').animate({
                                scrollTop: $("#scrolltopid").offset().top
                            }, 2000);
                           }                           
                       }
                    }
                });
            });
            $(document).on('change','#ifthengive_option_amount', function (){                
                jQuery('#ifthengive_fixed_price_span_select').html('').html($(this).val());
            });
            
            $(document).on('keyup','#itg_manual_amount_input', function (){
                var amt = parseFloat($(this).val()).toFixed(2);
                if(isNaN(amt)){
                    jQuery('#ifthengive_manual_price_span').html('').html('50.00');
                }else{
                    jQuery('#ifthengive_manual_price_span').html('').html(amt);
                }
                
            });
            $(document).on('input','#itg_manual_amount_input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            });
            
            $(document).on('click','#itg_signup_as_guest',function() {
                if($(this).is(":checked")) {
                    $(".itg-password").show(300);
                } else {
                    $(".itg-password").hide(200);
                }
            });
            
            //Check if function exists
            $.fn.exists = function () {
                return this.length > 0;
            };
            var $tabNavigation = $(".tabs-navigation span");
            if ($tabNavigation.exists()) {
                $(document).on("click", '.tabs-navigation span',function (e) {                    
                    $(this).tab("show"), e.preventDefault();
                    if($(this).attr('href') == '#itg_account_goals'){                        
                        $('#IfThenGive_Goals_Table').DataTable().columns.adjust().responsive.recalc();
                    }
                    return false;
                });
            }
            
            $(document).on('click','#itg_account_cancel_baid',function(){
                var userid = $('#itg_account_cancel_baid').attr('data-userid');
                alertify.confirm('Cancel Billing Agreement', 'All goals will be permanently canceled and a new agreement would have to be setup in order to give again.',
                function ()
                {
                    $.ajax({
                       type: 'POST',
                       url: admin_ajax_url,
                        data: { 
                           action  : 'cancel_my_account_ba',
                           userid : userid
                       },                       
                       beforeSend: function () {
                         $('#canceel_baid_overlay').show();
                       },
                       complete: function(){
                         $('#canceel_baid_overlay').hide();
                       },
                       success: function (result) {
                           $('#cancel_ba_error_public').show();
                           $('#itg_cancel_ba_msg').html(result);                        
                       }
                    });
                },
                function ()
                {
                    alertify.error('You Pressed Cancel');
                }); 
            });

            
        });
})( jQuery );
