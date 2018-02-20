(function ($) {
    'use strict';
    jQuery(document).ready(function ($) {
        //$(document).on('click', '#ifthengive_angelleye_checkout', function () {
                        
        //});
        $(document).on('change', '#ifthengive_option_amount', function () {
            jQuery('#ifthengive_fixed_price_span_select').html('').html($(this).val());
        });

        $(document).on('keyup', '#itg_manual_amount_input', function () {
            var amt = parseFloat($(this).val()).toFixed(2);
            if (isNaN(amt)) {
                jQuery('#ifthengive_manual_price_span').html('').html('50.00');
            } else {
                jQuery('#ifthengive_manual_price_span').html('').html(amt);
            }

        });
        $(document).on('input', '#itg_manual_amount_input', function () {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        });

        $(document).on('click', '.itg_signup_as_guest', function () {
            if ($(this).is(":checked")) {
                $(this).parents('form').find(".itg-password").show(300);
            } else {
                $(this).parents('form').find(".itg-password").hide(200);
            }
        });

        //Check if function exists
        $.fn.exists = function () {
            return this.length > 0;
        };
        var $tabNavigation = $(".tabs-navigation span");
        if ($tabNavigation.exists()) {
            $(document).on("click", '.tabs-navigation span', function (e) {
                $(this).tab("show"), e.preventDefault();
                if ($(this).attr('href') == '#itg_account_goals') {
                    $('#IfThenGive_Goals_Table').DataTable().columns.adjust().responsive.recalc();
                }
                return false;
            });
        }

        $(document).on('click', '#itg_account_cancel_baid', function () {
            var userid = $('#itg_account_cancel_baid').attr('data-userid');
            alertify.confirm('Cancel Billing Agreement', 'All goals will be permanently canceled and a new agreement would have to be setup in order to give again.',
                    function ()
                    {
                        $.ajax({
                            type: 'POST',
                            url: admin_ajax_url,
                            data: {
                                action: 'cancel_my_account_ba',
                                userid: userid
                            },
                            beforeSend: function () {
                                $('#canceel_baid_overlay').show();
                            },
                            complete: function () {
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
        if($('.itg-paypal-form').length > 0){
            $('.itg-paypal-form').each(function(){
                var itgPaypalForm = $(this);
                var pid=$(this).data('id');
                var ppcontainer = 'paypal-button-container-'+$(this).data('id');
                paypal.Button.render({
                env: 'sandbox', // sandbox | production
                // Show the buyer a 'Pay Now' button in the checkout flow
                style: {
                    label: 'generic',
                    size:  'medium',    // small | medium | large | responsive
                    shape: 'rect',     // pill | rect
                    color: 'gold',     // gold | blue | silver | black
                    tagline: false,
                    branding : true                    
                },
                // payment() is called when the button is clicked
                payment: function () {
                    var amount = '';
                    var formData = '';
                    var user_id = '';
                    if ($(this).find('#ifthengive_fixed_price_span').length) {
                        amount = itgPaypalForm.find('#ifthengive_fixed_price_span').html();
                    }
                    if ($(this).find('#ifthengive_fixed_price_span_select').length) {
                        amount = itgPaypalForm.find('#ifthengive_fixed_price_span_select').html();
                    }
                    if ($(this).find('#ifthengive_manual_price_span').length) {
                        amount = itgPaypalForm.find('#ifthengive_manual_price_span').html();
                    }
                    formData = itgPaypalForm.find("#ifthengive_signup_"+pid).serialize();
                    user_id = itgPaypalForm.find('#ifthengive_angelleye_checkout'+pid).attr('data-userid');

                    var CREATE_URL = admin_ajax_url;
                    var data = {
                        action: 'start_express_checkout',
                        post_id: itgPaypalForm.data('id'),
                        amount: amount,
                        formData: formData,
                        login_user_id: user_id
                    };
                    // Make a call to your server to set up the payment
                    return paypal.request.post(CREATE_URL, data)
                            .then(function (result) {
                                //console.log(result);
                                if (result.Ack == 'Success') {
                                    $('#ifthengive_signup_'+pid)[0].reset();
                                    return result.paymentID;
                                } else {
                                    console.log(result.Ack)
                                    if (result.Ack == 'ValidationError') {
                                        itgPaypalForm.find('#overlay').hide();
                                        itgPaypalForm.find('#connect_paypal_error_public').show();
                                        itgPaypalForm.find('#connect_paypal_error_p').html('').html('<strong>Acknowledgement :</strong> ' + result.Ack);
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<br><strong>Error Code :</strong> ' + result.ErrorCode);
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<br><strong>Long Message :</strong> ' + result.ErrorLong);
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<br><strong>Errors :</strong>');
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<ul>');
                                        jQuery.each(result.Errors, function (i, val) {
                                           itgPaypalForm.find('#connect_paypal_error_p').append('<li>' + val + '</li>');
                                        });
                                        itgPaypalForm.find('#connect_paypal_error_p').append('</ul>');
                                        $('html, body').animate({
                                            scrollTop: $("#scrolltopid").offset().top
                                        }, 2000);
                                    } else {
                                        itgPaypalForm.find('#overlay').hide();
                                        itgPaypalForm.find('#connect_paypal_error_public').show();
                                        itgPaypalForm.find('#connect_paypal_error_p').html('').html('<strong>Acknowledgement :</strong> ' + result.Ack);
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<br><strong>Error Code :</strong> ' + result.ErrorCode);
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<br><strong>Short Message :</strong> ' + result.ErrorShort);
                                        itgPaypalForm.find('#connect_paypal_error_p').append('<br><strong>Long Message :</strong> ' + result.ErrorLong);
                                        itgPaypalForm.find('#ifthengive_signup_'+pid)[0].reset();
                                        $('html, body').animate({
                                            scrollTop: $("#scrolltopid").offset().top
                                        }, 2000);
                                    }
                                    //return false;
                                }                                
                            });
                },
                // onAuthorize() is called when the buyer approves the payment
                onAuthorize: function (data, actions) {
                    //console.log(data);
                    //console.log(actions);
                    actions.redirect();
                },
                onError: function (err) {
                    console.log(err);
                    // Show an error page here, when an error occurs
                },
                onCancel: function (data, actions) {
                    alert('The payment was cancelled!');
                    actions.redirect();
                }
            }, '#'+ppcontainer);
            })
            
        }
    });
})(jQuery);
