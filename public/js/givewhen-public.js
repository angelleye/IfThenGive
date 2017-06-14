(function( $ ) {
	'use strict';
        jQuery(document).ready(function ($) {
            $(document).on('click','#give_when_angelleye_checkout',function(){
                var amount='';
                if ($('#give_when_fixed_price_span').length) {
                    amount = $('#give_when_fixed_price_span').html();
                }
                if($('#give_when_fixed_price_span_select').length){
                    amount = $('#give_when_fixed_price_span_select').html();
                }
                
                $.ajax({
                    type: 'POST',
                    url: admin_ajax_url,
                     data: { 
                        action: 'start_express_checkout',
                        post_id : $(this).attr('data-postid'),
                        amount : amount
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
                           $('#connect_paypal_error_public').show();
                           $('#connect_paypal_error_p').html('').html('<strong>PayPal Acknowledgement :</strong> ' + result.Ack);                           
                           $('#connect_paypal_error_p').append('<br><strong>Error Code :</strong> ' + result.ErrorCode);                                                   
                           $('#connect_paypal_error_p').append('<br><strong>Short Message :</strong> ' + result.ErrorShort);
                           $('#connect_paypal_error_p').append('<br><strong>Long Message :</strong> ' + result.ErrorLong);                           
                           $('#connect_paypal_error_p').append('<br>Please Try Again.');
                       }
                    }
                });
            });
            $(document).on('change','#give_when_option_amount', function (){                
                jQuery('#give_when_fixed_price_span_select').html('').html($(this).val());
            });
        });
})( jQuery );
