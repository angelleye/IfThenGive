(function( $ ) {
	'use strict';
        jQuery(document).ready(function ($) {
            $(document).on('click','#give_when_angelleye_checkout',function(){
                $.ajax({
                    type: 'POST',
                    url: admin_ajax_url,
                     data: { 
                        action: 'start_express_checkout',
                        post_id : $(this).attr('data-postid')
                    },
                    dataType: "json",
                    beforeSend: function () {
                        //$('#overlay').show();
                    },                
                    success: function (result) {
                       if(result.Ack == 'Success'){
                           //window.location.href = result.RedirectURL;
                       }
                       else{
//                           $('#overlay').hide();
//                           $('#connect_paypal_error').show();
//                           $('#connect_paypal_error_p').html('').html('PayPal Acknowledgement : ' + result.Ack);
//                           $('#connect_paypal_error_p').append('<br>Message : ' + result.Message);
//                           $('#connect_paypal_error_p').append('<br>ErrorID : ' + result.ErrorID);
//                           $('#connect_paypal_error_p').append('<br>Please Try Again later');
                       }
                    }
                });
            });
        });
})( jQuery );
