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
        add_action('give_when_shortcode_interface', array(__CLASS__, 'give_when_shortcode_interface_html'));
        add_action('give_when_givers_interface', array(__CLASS__, 'give_when_givers_interface_html'));
        add_action('give_when_do_transactions_interface', array(__CLASS__, 'give_when_do_transactions_interface_html'));
        add_action('give_when_list_transactions_interface', array(__CLASS__, 'give_when_list_transactions_interface_html'));
        add_action('give_when_get_transaction_detail', array(__CLASS__, 'give_when_get_transaction_detail_html'));
        add_action('give_when_retry_failed_transactions_interface', array(__CLASS__, 'give_when_retry_failed_transactions_interface_html'));
        add_action('give_when_disconnect_interface',array(__CLASS__,'give_when_disconnect_interface_html'));
        add_action('admin_head', array(__CLASS__, 'give_when_hide_publish_button_until'));
    }

    /**
     * give_when_interface_html function is for
     * html of interface when action is Edit.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_interface_html() {
        $conncet_to_paypal_flag = get_option('give_when_permission_connected_to_paypal');
        if ($conncet_to_paypal_flag != 'Yes') {
            ?>
            <div style="padding-top: 25px"></div>
            <div class="container" style="max-width: 100%">
                <div class="bs-callout bs-callout-warning">
                    <h4><?php echo __('You are not Connected with PayPal.', 'angelleye_give_when'); ?></h4>
                    <a href="<?php echo site_url(); ?>/wp-admin/options-general.php?page=give_when_option"><?php echo __('Click Here', 'angelleye_give_when'); ?></a><?php echo __(' for Setting page to Connect With PayPal.', 'angelleye_give_when'); ?>
                </div>               
            </div>
            <?php
        } else {
            $action_request = isset($_REQUEST['view']) ? $_REQUEST['view'] : '';
            global $post;
            $trigger_name = get_post_meta($post->ID, 'trigger_name', true);
            $trigger_thing = get_post_meta($post->ID, 'trigger_thing', true);
            $trigger_desc = get_post_meta($post->ID, 'trigger_desc', true);
            $image_url = get_post_meta($post->ID, 'image_url', true);
            $gw_amount = get_post_meta($post->ID, 'amount', true);
            if ($gw_amount == 'fixed') {
                $fixed_amount_check = 'checked';
                $fixed_amount_input_class = "";
                $fixed_amount_input_value = get_post_meta($post->ID, 'fixed_amount_input', true);
            } else {
                $fixed_amount_check = '';
                $fixed_amount_input_class = "hidden";
                $fixed_amount_input_value = '';
            }

            if ($gw_amount == 'select') {
                $dynamic_options_check = 'checked';
                $dynamic_options_class = '';
                $dynamic_options_name = get_post_meta($post->ID, 'option_name', true);
                $dynamic_option_amount = get_post_meta($post->ID, 'option_amount', true);
            } else {
                $dynamic_options_check = '';
                $dynamic_options_class = 'hidden';
            }

            if ($gw_amount == 'manual') {
                $manual_options_check = 'checked';
            } else {
                $manual_options_check = '';
            }

            $screen = get_current_screen();
            if ($screen->action == 'add') {
                $fixed_amount_check = 'checked';
                $fixed_amount_input_class = "";
            }
            ?>

            <div style="padding-top: 25px"></div>
            <div class="container" style="max-width: 100%">   
                <form>
                    <div class="form-group">
                        <label for="triggerName" class="control-label"><?php echo __('Goal Name', 'angelleye_give_when'); ?></label>
                        <input type="text" name="trigger_name" value="<?php echo $trigger_name ?>" class="form-control" autocomplete="off" id="trigger_name" placeholder="Enter Name Here"/>
                    </div>
                    <div class="form-group">
                        <label for="triggerName" class="control-label"><?php echo __('Thing', 'angelleye_give_when'); ?></label>
                        <input type="text" name="trigger_thing" value="<?php echo $trigger_thing ?>" class="form-control" autocomplete="off" id="trigger_name" placeholder="Enter event Here"/>
                    </div>
                    <div class="form-group">
                        <label for="triggerDesc" class="control-label"><?php echo __('Goal Description', 'angelleye_give_when'); ?></label>
                        <textarea name="trigger_desc" class="form-control" autocomplete="off" id="trigger_desc" placeholder="Enter Description Here"><?php echo $trigger_desc; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image_url"><?php echo __('Image', 'angelleye_give_when'); ?></label>
                        <input type="text" name="image_url" id="image_url" class="form-control" value="<?php echo $image_url; ?>"><br>
                        <input type="button" name="upload-btn" id="upload-btn" class="btn btn-primary" value="Upload Image">
                    </div>                
                    <div class="form-group">
                        <input type="radio" name="fixed_radio" id="fixed_radio" value="fixed" <?php echo $fixed_amount_check; ?>><label class="radio-inline" for="fixed_radio"><strong><?php echo __('Fixed', 'angelleye_give_when'); ?></strong></label>
                        &nbsp;
                        <input type="radio" name="fixed_radio" id="option_radio" value="select" <?php echo $dynamic_options_check; ?>><label class="radio-inline" for="option_radio"><strong><?php echo __('Select', 'angelleye_give_when'); ?></strong></label>
                        &nbsp;
                        <input type="radio" name="fixed_radio" id="manual_radio" value="manual" <?php echo $manual_options_check; ?>><label class="radio-inline" for="manual_radio"><strong><?php echo __('Allow User to Manually Add', 'angelleye_give_when'); ?></strong></label>
                    </div>                

                    <div class="form-group <?php echo $fixed_amount_input_class; ?>" id="fixed_amount_input_div">
                        <label for="triggerName" class="control-label"><?php echo __('Fixed Amount', 'angelleye_give_when'); ?></label>
                        <input type="text" name="fixed_amount_input" value="<?php echo $fixed_amount_input_value; ?>" class="form-control" autocomplete="off" id="fixed_amount_input" placeholder="Enter Amount"/>
                    </div>

                    <div id="dynamic_options" class="<?php echo $dynamic_options_class; ?>">                    
            <?php
            if (!empty($dynamic_options_name)) {
                $i = 0;
                $total_options = count($dynamic_options_name);
                ?> <div id="education_fields"> 
                            <?php foreach ($dynamic_options_name as $name) { ?>       
                                    <div class="form-group removeclass<?php echo ($i + 1); ?>">
                                        <div class="col-sm-1 nopadding">
                                            <label class="control-label"><?php echo __('Option', 'angelleye_give_when'); ?> </label>
                                        </div>
                                        <div class="col-sm-3 nopadding">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="option_name" name="option_name[]" value="<?php echo $name; ?>" placeholder="Option Name">
                                            </div>
                                        </div>                
                                        <div class="col-sm-3 nopadding">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="option_amount" name="option_amount[]" value="<?php echo $dynamic_option_amount[$i]; ?>" placeholder="Option Amount">
                                                    <div class="input-group-btn">
                    <?php if (($i + 1) == $total_options) { ?>
                                                            <button class="btn btn-success" type="button" id="add_dynamic_field"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> </button>
                                                        <?php } else { ?>                                            
                                                            <button class="btn btn-danger" type="button" id="remove_dynamic_fields" data-fieldid="<?php echo ($i + 1); ?>"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button>                                            
                                                        <?php } ?>    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>

                    <?php $i++;
                } ?> </div> <?php
            } else {
                ?>
                            <div id="education_fields">
                                <div class="col-sm-1 nopadding">
                                    <label class="control-label"><?php echo __('Option', 'angelleye_give_when'); ?> </label>
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
            <?php } ?>                    
                    </div>    
                </form>
            </div>
        <?php
        }
    }

    /**
     * give_when_shortcode_interface_html function is for
     * html of interface when action is View.
     * @since 1.0.0
     * @access public
     */
    public static function give_when_shortcode_interface_html() {
        global $post, $post_ID;
        ?>        
        <div class="give_when_container">
            <div class="row">
                <div class="col-md-12">
                    <p><?php echo __('You can easily place this button in your pages and posts using this tool....', 'angelleye_give_when'); ?></p>
                    <img src="<?php echo GW_PLUGIN_URL; ?>/admin/images/give_when_tool.png" class="img-responsive" style="margin: 0 auto;"/>
                </div>
            </div>
            <div class="row">
                <div class="text-center"><?php echo __('OR', 'angelleye_give_when'); ?></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p><?php echo __('You may copy and paste this shortcode into any Page or Post to place the "Goal" where you would like it to be displayed.', 'angelleye_give_when'); ?></p>
                    <div class="zero-clipboard"><span class="btn-clipboard" data-toggle="tooltip" data-placement="left" title="Copy To Clipboard"><?php echo __('Copy', 'angelleye_give_when'); ?></span></div>
                    <div class="give_when_highlight">
                        <h4><?php echo '[give_when_goal id=' . $post_ID . ']'; ?></h4>                        
                    </div>
                </div>
            </div>
        </div>        
        <script type="text/javascript">
            jQuery('[data-toggle="tooltip"]').tooltip();

            var clipboard = new Clipboard('.btn-clipboard', {
                target: function () {
                    return document.querySelector('.give_when_highlight');
                }
            });
            /* Below code will use whenever we want further clipboard work */
            /*clipboard.on('success', function(e) {
             console.log(e);
             });
                     
             clipboard.on('error', function(e) {
             console.log(e);
             });*/
        </script>
        <?php
    }

    public static function give_when_givers_interface_html() {
        global $post, $post_ID;
        ?>
        <div class="wrap">            
            <div class="give_when_container">
                <div class="row">
                    <div class="col-md-12 text-center">
        <?php
        $trigger_name = get_post_meta($_REQUEST['post'], 'trigger_name', true);
        ?>
                        <h3 class="text-info"><?php echo __('Givers For ', 'angelleye_give_when'); ?><?php echo $trigger_name; ?> </h3>
                    </div>                
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <span class="text-info"><?php echo __('Click ', 'angelleye_give_when'); ?><strong>"FUN"</strong><?php echo __(' Button to Capture your Transactions.', 'angelleye_give_when'); ?></span><br/>                    
                        <a class="btn btn-primary btn-lg" id="give_when_fun" href="<?php echo site_url(); ?>/wp-admin/?page=give_when_givers&post=<?php echo $_REQUEST['post']; ?>&view=DoTransactions" onclick="return confirm('Ready to process payments based on this goal occurrence?')">Fun</a>
                    </div>
                    <div class="col-md-12">
                        <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=give_when_goals'; ?>">Back to Goals</a>
                    </div>
                </div>            
                <div class="row">
                    <div class="col-md-12">                                     
        <?php
        $table = new AngellEYE_Give_When_Givers_Table();
        $table->prepare_items();
        echo '<form method="post" action="">';
        $table->search_box('Search', 'givers_search_id');
        $table->display();
        echo '</form>';
        ?>                    
                    </div>                
                </div>
            </div>   
        </div>   
        <?php
    }

    public static function give_when_do_transactions_interface_html() {
        @set_time_limit(GW_PLUGIN_SET_TIME_LIMIT);
        @ignore_user_abort(true);
        $EmailString = '';
        $EmailString.='<style>.table {
                                width: 100%;
                                max-width: 100%;
                                margin-bottom: 20px;
                                background-color: transparent;
                                border-spacing: 0;
                                border-collapse: collapse;
                                }
                                .table-striped > tbody > tr:nth-of-type(odd){
                                    background-color: #f9f9f9;
                                }
                                .table > thead > tr > th, 
                                .table > tbody > tr > th, 
                                .table > tfoot > tr > th, 
                                .table > thead > tr > td, 
                                .table > tbody > tr > td, 
                                .table > tfoot > tr > td {                                
                                    padding: 8px;
                                    line-height: 1.42857143;
                                    vertical-align: top;
                                    border-top: 1px solid #ddd;
                                }
                                .alert {
                                    padding: 15px;
                                    margin-bottom: 20px;
                                    border: 1px solid transparent;
                                    border-radius: 4px;
                                }
                                .alert-info {
                                    color: #31708f;
                                    background-color: #d9edf7;
                                    border-color: #bce8f1;
                                }
                                p {
                                    margin: 0 0 10px;
                                }
                                .alert > p, .alert > ul {
                                    margin-bottom: 0;
                                }
                       </style>';
        if (ob_get_level() == 0)
            ob_start();
        ?>
        <div class="wrap">
            <div class="give_when_container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?php _e('Capturing Transactions','angelleye_give_when'); ?></div>
                            <div class="panel-body">
                                <div class="table-responsive">
        <?php
        echo $EmailString.='<table class="table table-striped">
                                            <tr>
                                                <th>Transaction ID</th>
                                                <th>Amount</th>
                                                <th>Payer Email</th>
                                                <th>PayPal ACK</th>
                                                <th>Payment Status</th>
                                            </tr>';
        global $post, $post_ID;
        $goal_id = $_REQUEST['post'];
        $trigger_name = get_post_meta($goal_id, 'trigger_name', true);
        $givers = AngellEYE_Give_When_Givers_Table::get_all_givers();
        $PayPal_config = new Give_When_PayPal_Helper();        
        $PayPal_config->set_api_cedentials();        
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        $total_txn = 0;
        $total_txn_success = 0;
        $total_txn_failed = 0;
        foreach ($givers as $value) {
            $trigger_name = get_post_meta($_REQUEST['post'], 'trigger_name', true);
            $desc = !empty($trigger_name) ? $trigger_name : '';

            $DRTFields = array(
                'referenceid' => $value['BillingAgreement'],
                'paymentaction' => 'Sale',
            );

            $PaymentDetails = array(
                'amt' => $value['amount'],
                //'currencycode' => $value['give_when_gec_currency_code'],
                'desc' => $desc,
                'custom' => 'user_id_' . $value['user_id'] . '|post_id_' . $_REQUEST['post'],
            );

            $PayPalRequestData = array(
                'DRTFields' => $DRTFields,
                'PaymentDetails' => $PaymentDetails,
            );
            $PayPalResultDRT = $PayPal->DoReferenceTransaction($PayPalRequestData);
            //save log
            $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
            if ('yes' == $debug) {
                $log_write = new AngellEYE_Give_When_Logger();
                $log_write->add('angelleye_give_when_transactions', 'DoReferenceTransaction ' . $PayPalResultDRT['ACK'] . ' : ' . print_r($PayPalResultDRT, true), 'transactions');
            }
            $paypal_email = get_user_meta($value['user_id'], 'give_when_gec_email', true);
            if ($PayPal->APICallSuccessful($PayPalResultDRT['ACK'])) {

                $total_txn_success++;
                echo $trEmailString = "<tr>
                    <td>{$PayPalResultDRT['TRANSACTIONID']}</td>
                    <td>{$PayPalResultDRT['AMT']}</td>
                    <td>{$paypal_email}</td>
                    <td>{$PayPalResultDRT['ACK']}</td>
                    <td>{$PayPalResultDRT['PAYMENTSTATUS']}</td>
                </tr>";
                $EmailString.= $trEmailString;
            } else {
                $total_txn_failed++;
                $PayPalResultDRT['TRANSACTIONID'] = '';

                echo $trEmailString = "<tr>
                    <td>-</td>
                    <td>{$value['amount']}</td>
                    <td>{$paypal_email}</td>
                    <td>{$PayPalResultDRT['ACK']}</td>
                    <td>-</td>
                </tr>";
                $EmailString.= $trEmailString;
            }
            $new_post_id = wp_insert_post(array(
                'post_status' => 'publish',
                'post_type' => 'gw_transactions',
                'post_title' => ('UserID:' . $value['user_id'] . '|GoalID:' . $goal_id . '|TxnID :' . $PayPalResultDRT['TRANSACTIONID'])
                    ));
            update_post_meta($new_post_id, 'give_when_transactions_amount', $value['amount']);
            update_post_meta($new_post_id, 'give_when_transactions_wp_user_id', $value['user_id']);
            update_post_meta($new_post_id, 'give_when_transactions_wp_goal_id', $goal_id);
            update_post_meta($new_post_id, 'give_when_transactions_transaction_id', $PayPalResultDRT['TRANSACTIONID']);
            update_post_meta($new_post_id, 'give_when_transactions_ack', $PayPalResultDRT['ACK']);
            ?>
                                        <?php
                                        $total_txn++;
                                        ob_flush();
                                        flush();
                                        sleep(2);
                                    }
                                    ?>              <?php echo $endtabeEmailString = "</table>";
                            $EmailString.=$endtabeEmailString; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo $alert_info_email_string = '<div class="alert alert-info">
                    <p>Total Transactions : <strong>' . $total_txn . '</strong></p>
                    <p>Total Successful Transactions : <strong>' . $total_txn_success . '</strong></p>
                    <p>Total Failed Transactions : <strong>' . $total_txn_failed . '</strong></p>
                </div>';
                        $EmailString.=$alert_info_email_string;

                        $headers = "From: info@givewhen.com \r\n";
                        $headers .= "Reply-To: noreply@givewhen.com \r\n";
                        //$headers .= "CC: susan@example.com\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                        $to = $admin_email = get_option('admin_email');
                        $subject = 'GiveWhen Transaction Report For ' . $trigger_name;
                        $message = $EmailString;
                        wp_mail($to, $subject, $message, $headers);
                        ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=give_when_goals'; ?>">Back To Goals</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ob_end_flush();
    }

    public static function give_when_list_transactions_interface_html() {
        global $post, $post_ID;
        ?>
        <div class="wrap">
            <div class="give_when_container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php
                        $trigger_name = get_post_meta($_REQUEST['post'], 'trigger_name', true);
                        ?>
                        <h3 class="text-info"><?php _e('Transactions for ','angelleye_give_when'); ?> <?php echo __($trigger_name,'angelleye_give_when') ; ?></h3>
                    </div>
                    <div class="col-md-12">
                        <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=give_when_goals'; ?>">Back to Goals</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form method="post">
                            <?php
                            $table = new AngellEYE_Give_When_Transactions_Table();
                            $table->prepare_items();
                            $table->search_box('Search', 'givers_transaction_search_id');
                            $table->display();
                            ?>
                        </form>
                    </div>                
                </div>
            </div>        
        </div>
        <?php
    }

    public static function give_when_get_transaction_detail_html() {
        $transaction_id = $_REQUEST['txn_id'];
        global $post, $post_ID;
        $goal_id = $post_ID;
        $givers = AngellEYE_Give_When_Givers_Table::get_all_givers();
        $PayPal_config = new Give_When_PayPal_Helper();
        $PayPal_config->set_api_cedentials();
        $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
        $GTDFields = array(
            'transactionid' => $transaction_id
        );
        $PayPalRequestData = array('GTDFields' => $GTDFields);
        $PayPalResultTransactionDetail = $PayPal->GetTransactionDetails($PayPalRequestData);
        if ($PayPal->APICallSuccessful($PayPalResultTransactionDetail['ACK'])) {
            $requestString = $PayPalResultTransactionDetail['RAWREQUEST'];
            $responseString = $PayPalResultTransactionDetail['RAWRESPONSE'];
            $requestData = $PayPalResultTransactionDetail['REQUESTDATA'];
            ?>
            <div class="wrap">
                <div class="give_when_container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="text-info"><?php _e('Transaction Details','angelleye_give_when'); ?></h3>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=give_when_goals'; ?>"><?php _e('Back To Goals','angelleye_give_when'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <?php _e('Transaction Id ','angelleye_give_when'); ?> <?php echo '#' . $_REQUEST['txn_id']; ?> 
                                </div>
                                <div class="panel-body">
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payer Email :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['EMAIL']) ? $PayPalResultTransactionDetail['EMAIL']: ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payer ID :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PAYERID']) ? $PayPalResultTransactionDetail['PAYERID'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Country Code :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['COUNTRYCODE']) ? $PayPalResultTransactionDetail['COUNTRYCODE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Goal Name :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['SUBJECT']) ? $PayPalResultTransactionDetail['SUBJECT'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payer PayPal Name :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php 
                                        $fname = isset($PayPalResultTransactionDetail['FIRSTNAME']) ? $PayPalResultTransactionDetail['FIRSTNAME'] : '';
                                        $lname = isset($PayPalResultTransactionDetail['LASTNAME']) ? $PayPalResultTransactionDetail['LASTNAME'] : '';
                                        echo  $fname. ' ' .$lname ; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Transaction ID :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['TRANSACTIONID']) ? $PayPalResultTransactionDetail['TRANSACTIONID'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Transaction Type :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['TRANSACTIONTYPE']) ? $PayPalResultTransactionDetail['TRANSACTIONTYPE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payment Type :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PAYMENTTYPE']) ? $PayPalResultTransactionDetail['PAYMENTTYPE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Amount :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['AMT']) ? $PayPalResultTransactionDetail['AMT'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Payment Status :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PAYMENTSTATUS']) ? $PayPalResultTransactionDetail['PAYMENTSTATUS'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Pending Reason :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <?php echo isset($PayPalResultTransactionDetail['PENDINGREASON']) ? $PayPalResultTransactionDetail['PENDINGREASON'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Request String :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-10" style="word-wrap: break-word;">
                                        <?php echo isset($PayPalResultTransactionDetail['RAWREQUEST']) ? $PayPalResultTransactionDetail['RAWREQUEST'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-5">
                                        <pre><?php print_r($PayPal->NVPToArray($PayPalResultTransactionDetail['RAWREQUEST'])); ?></pre>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2">
                                        <label class="text-primary"><?php _e('Response String :','angelleye_give_when'); ?></label>
                                    </div>
                                    <div class="col-md-10" style="word-wrap: break-word;">
                                        <?php echo isset($PayPalResultTransactionDetail['RAWRESPONSE']) ? $PayPalResultTransactionDetail['RAWRESPONSE'] : ''; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-5">
                                        <pre><?php print_r($PayPal->NVPToArray($PayPalResultTransactionDetail['RAWRESPONSE'])); ?></pre>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>                        
                            </div>
                        </div>                
                    </div>
                </div>        
            </div>
            <?php
        } else {
            // errors in acknowledgement 
        }
    }

    public static function give_when_retry_failed_transactions_interface_html() {
        @set_time_limit(GW_PLUGIN_SET_TIME_LIMIT);
        @ignore_user_abort(true);
        $EmailString = '';
        $EmailString.='<style>.table {
                                width: 100%;
                                max-width: 100%;
                                margin-bottom: 20px;
                                background-color: transparent;
                                border-spacing: 0;
                                border-collapse: collapse;
                                }
                                .table-striped > tbody > tr:nth-of-type(odd){
                                    background-color: #f9f9f9;
                                }
                                .table > thead > tr > th, 
                                .table > tbody > tr > th, 
                                .table > tfoot > tr > th, 
                                .table > thead > tr > td, 
                                .table > tbody > tr > td, 
                                .table > tfoot > tr > td {                                
                                    padding: 8px;
                                    line-height: 1.42857143;
                                    vertical-align: top;
                                    border-top: 1px solid #ddd;
                                }
                                .alert {
                                    padding: 15px;
                                    margin-bottom: 20px;
                                    border: 1px solid transparent;
                                    border-radius: 4px;
                                }
                                .alert-info {
                                    color: #31708f;
                                    background-color: #d9edf7;
                                    border-color: #bce8f1;
                                }
                                p {
                                    margin: 0 0 10px;
                                }
                                .alert > p, .alert > ul {
                                    margin-bottom: 0;
                                }
                       </style>';
        if (ob_get_level() == 0)
            ob_start();
        ?>
        <div class="wrap">
            <div class="give_when_container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><?php _e('Capturing Failure Payments','angelleye_give_when'); ?></div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <?php
                                    echo $EmailString.='<table class="table table-striped">
                                            <tr>
                                                <th>Transaction ID</th>
                                                <th>Amount</th>
                                                <th>Payer Email</th>
                                                <th>PayPal ACK</th>
                                                <th>Payment Status</th>
                                            </tr>';
                                    global $post, $post_ID;
                                    $goal_id = $_REQUEST['post'];
                                    $trigger_name = get_post_meta($goal_id, 'trigger_name', true);
                                    $givers = AngellEYE_Give_When_Transactions_Table::get_all_failed_givers($goal_id);
                                    $PayPal_config = new Give_When_PayPal_Helper();                                    
                                    $PayPal_config->set_api_cedentials();                                    
                                    $PayPal = new \angelleye\PayPal\PayPal($PayPal_config->get_configuration());
                                    $total_txn = 0;
                                    $total_txn_success = 0;
                                    $total_txn_failed = 0;
                                    foreach ($givers as $value) {
                                        $trigger_name = get_post_meta($_REQUEST['post'], 'trigger_name', true);
                                        $desc = !empty($trigger_name) ? $trigger_name : '';

                                        $DRTFields = array(
                                            'referenceid' => $value['BillingAgreement'],
                                            'paymentaction' => 'Sale',
                                        );

                                        $PaymentDetails = array(
                                            'amt' => $value['amount'],
                                            //'currencycode' => $value['give_when_gec_currency_code'],
                                            'desc' => $desc,
                                            'custom' => 'user_id_' . $value['user_id'] . '|post_id_' . $_REQUEST['post'],
                                        );

                                        $PayPalRequestData = array(
                                            'DRTFields' => $DRTFields,
                                            'PaymentDetails' => $PaymentDetails,
                                        );
                                        $PayPalResultDRT = $PayPal->DoReferenceTransaction($PayPalRequestData);
                                        //save log
                                        $debug = (get_option('log_enable_give_when') == 'yes') ? 'yes' : 'no';
                                        if ('yes' == $debug) {
                                            $log_write = new AngellEYE_Give_When_Logger();
                                            $log_write->add('angelleye_give_when_transactions', 'DoReferenceTransaction ' . $PayPalResultDRT['ACK'] . ' : ' . print_r($PayPalResultDRT, true), 'transactions');
                                        }
                                        $paypal_email = get_user_meta($value['user_id'], 'give_when_gec_email', true);
                                        if ($PayPal->APICallSuccessful($PayPalResultDRT['ACK'])) {
                                            update_post_meta($value['post_id'], 'give_when_transactions_transaction_id', $PayPalResultDRT['TRANSACTIONID']);
                                            $total_txn_success++;
                                            echo $trEmailString = "<tr>
                    <td>{$PayPalResultDRT['TRANSACTIONID']}</td>
                    <td>{$PayPalResultDRT['AMT']}</td>
                    <td>{$paypal_email}</td>
                    <td>{$PayPalResultDRT['ACK']}</td>
                    <td>{$PayPalResultDRT['PAYMENTSTATUS']}</td>
                </tr>";
                                            $EmailString.= $trEmailString;
                                        } else {
                                            $total_txn_failed++;
                                            $PayPalResultDRT['TRANSACTIONID'] = '';

                                            echo $trEmailString = "<tr>
                    <td>-</td>
                    <td>{$value['amount']}</td>
                    <td>{$paypal_email}</td>
                    <td>{$PayPalResultDRT['ACK']}</td>
                    <td>-</td>
                </tr>";
                                            $EmailString.= $trEmailString;
                                        }
                                        update_post_meta($value['post_id'], 'give_when_transactions_ack', $PayPalResultDRT['ACK']);
                                        ?>
                                        <?php
                                        $total_txn++;
                                        ob_flush();
                                        flush();
                                        sleep(2);
                                    }
                                    ?>              <?php echo $endtabeEmailString = "</table>";
                            $EmailString.=$endtabeEmailString; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo $alert_info_email_string = '<div class="alert alert-info">
                    <p>Total Transactions : <strong>' . $total_txn . '</strong></p>
                    <p>Total Successful Transactions : <strong>' . $total_txn_success . '</strong></p>
                    <p>Total Failed Transactions : <strong>' . $total_txn_failed . '</strong></p>
                </div>';
                        $EmailString.=$alert_info_email_string;

                        $headers = "From: info@givewhen.com \r\n";
                        $headers .= "Reply-To: noreply@givewhen.com \r\n";
                        //$headers .= "CC: susan@example.com\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

                        $to = $admin_email = get_option('admin_email');
                        $subject = 'GiveWhen Transaction Report For ' . $trigger_name;
                        $message = $EmailString;
                        wp_mail($to, $subject, $message, $headers);
                        ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a class="btn btn-info" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=give_when_goals'; ?>">Back To Goals</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        ob_end_flush();
    }
    
    public static function give_when_disconnect_interface_html() {
        
        update_option('give_when_permission_connected_to_paypal', 'no');
        delete_option('give_when_permission_connected_person_merchant_id');
        delete_option('give_when_permission_connected_person_email_id');

        $url = admin_url('admin.php?page=give_when_option&tab=connect_to_paypal');
        echo "<script>";
        echo 'window.location.href = "' . $url . '";';
        echo "</script>";
        die();
    }

    public static function give_when_hide_publish_button_until() {
        if (isset($_REQUEST['post_type'])) {
            if ($_REQUEST['post_type'] == 'give_when_goals') {
                $conncet_to_paypal_flag = get_option('give_when_permission_connected_to_paypal');
                if ($conncet_to_paypal_flag != 'Yes') {
                    ?>
                    <style>
                        #publishing-action { display: none; }
                        #save-action{display: none;}
                    </style>
                    <?php
                }
            }
        }
    }
}

AngellEYE_Give_When_interface::init();
