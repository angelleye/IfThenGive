<?php
/**
 * IfThenGive Transaction template.
 *
 * This template can be overriden by copying this file to your-theme/IfThenGive/template-ifthengive-my-goals.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	IfThenGive
 * @version     1.0.0
 */
if (!defined('ABSPATH'))
    exit; // Don't allow direct access
?>

<?php
if(! is_admin()){
?>
<div id="adjust_amount_overlay" style=" background: #d9d9da;opacity: 0.9;width: 100%;float: left;height: 100%;position: fixed;top: 0;left:0;right:0;z-index: 1031;text-align: center; display: none;">
    <div class="itg_loader"></div>
    <h1 style="font-weight: 600;">Processing...</h1>
</div>
<div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Goals', ITG_TEXT_DOMAIN) ?></abbr></div>
<div class="itg_center_container">   
    <div class="itgcontainer">
        <div class="itg_table-responsive">
            <table class="itg_table" id="IfThenGive_Goals_Table" width="100%">
                <thead>
                    <tr>
                        <th><?php _e('Goal Name', ITG_TEXT_DOMAIN); ?></th>                        
                        <th><?php _e('Amount', ITG_TEXT_DOMAIN); ?></th>           
                        <th><?php _e('Adjust Amount', ITG_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Action', ITG_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Date', ITG_TEXT_DOMAIN); ?></th>
                    </tr>
                </thead>
            </table>            
        </div>            
    </div>
</div>
<?php
$ccode = get_option('itg_currency_code');
$paypal = new AngellEYE_IfThenGive_PayPal_Helper();
$symbol = $paypal->get_currency_symbol($ccode);
?>
<script>
    jQuery(document).ready(function ($) {        
        var IfThenGive_Goals_Table = $('#IfThenGive_Goals_Table').dataTable({
            "serverSide": true,
            "responsive": true,
            "colReorder": true,
            "bRetrieve": true,
            "processing": true,
            "oLanguage": {"sEmptyTable": 'No Goals Found', "sZeroRecords": 'No records Found'},
            "ajax": {
                url: "<?php echo admin_url('admin-ajax.php'); ?>?action=ifthengive_my_goals",
                type: "POST"
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "render": function (data, type, row) {
                        return row.GoalName;
                    }
                },
                {
                    "targets": [1],
                    "render": function (data, type, row) {
                        var str = '<?php echo $symbol; ?>';
                        var amount = parseFloat(row.amount).toFixed(2);
                        return str + amount;
                    }
                },
                {
                    "targets": [2],'searchable': false,'orderable' : false,
                    "render": function (data, type, row) {
                        if(row.BillingAgreement == ''){
                            return '-';
                        }
                        else{
                            return '<button class="itg_btn itg_btn-primary itg_btn-sm itg_adjust_amount" data-goalamount="'+parseFloat(row.amount).toFixed(2)+'"  data-goalname="'+row.GoalName+'" data-goalid="'+row.goal_id+'" data-epostid="'+row.e_postId+'" data-userId="'+row.user_Id+'" ><?php _e('Adjust',ITG_TEXT_DOMAIN); ?></button>';
                        }
                    }
                },
                {
                    "targets": [3],'searchable': false,'orderable' : false,
                    "render": function (data, type, row) {
                        if(row.BillingAgreement == ''){
                            return '-';
                        }
                        else{
                            if(row.giver_status =='suspended'){
                                return '<button class="itg_btn itg_btn-sm itg_giver_status" data-changestatus="Active" data-goalname="'+row.GoalName+'" data-goalid="'+row.goal_id+'" data-epostid="'+row.e_postId+'" data-userId="'+row.user_Id+'" ><?php _e('Activate',ITG_TEXT_DOMAIN); ?></button>';
                            }
                            else{
                                return '<button class="itg_btn itg_btn-warning itg_btn-sm itg_giver_status" data-changestatus="Suspend"  data-goalname="'+row.GoalName+'" data-goalid="'+row.goal_id+'" data-epostid="'+row.e_postId+'" data-userId="'+row.user_Id+'" ><?php _e('Suspend',ITG_TEXT_DOMAIN); ?></button>';
                            }
                        }
                    }
                },    
                {
                    "targets": [4],
                    "render": function (data, type, row) {
                        return row.post_date;
                    }
                }                
            ]
        });  
            $(document).on('click','.itg_adjust_amount',function(e){
                var btn = $(this);
                var goalName = btn.attr('data-goalname');
                var goalId = btn.attr('data-goalid');
                var goalPostId = btn.attr('data-epostid');
                var goalUserId = btn.attr('data-userid');
                var goalAmount = btn.attr('data-goalamount');
                e.preventDefault();
                alertify.prompt( 'Adjust Amount for ' + goalName, 'Enter Amount', goalAmount,
                    function(evt, value) {
                        var changed_amount = parseFloat(value).toFixed(2);
                        if(isNaN(changed_amount)){
                            alertify.error('Please Enter Numeric Value.');
                            return false;
                        }
                        $.ajax({
                            type: 'POST',
                            url: admin_ajax_url,
                             data: { 
                                action  : 'itg_adjust_amount',
                                userid  : goalUserId,
                                postid  : goalPostId,
                                goalid  : goalId,
                                actual_amount  : goalAmount,
                                changed_amount : value
                            },                       
                            beforeSend: function () {
                              $('#adjust_amount_overlay').show();
                            },
                            complete: function(){
                              $('#adjust_amount_overlay').hide();
                            },
                            success: function (result) {
                                alertify.success('Amount changed for ' + goalName); 
                                IfThenGive_Goals_Table.api().ajax.reload();
                            }
                        });                        
                    }, 
                    function(){ 
                        alertify.error('You Pressed Cancel'); 
                    }
                );
            });
            
            $(document).on('click','.itg_giver_status',function(e){
                var btn = $(this);
                var chnageStatus = btn.attr('data-changestatus'); 
                var goalName = btn.attr('data-goalname');
                var goalId = btn.attr('data-goalid');
                var postId = btn.attr('data-epostid');
                var userId = btn.attr('data-userid');
                
                alertify.confirm(chnageStatus + ' for '+ goalName +'?', 'Are you sure you want to '+chnageStatus+' goal?',
                function ()
                {                                                            
                    $.ajax({
                       type: 'POST',
                       url: admin_ajax_url,
                        data: { 
                           action  : 'change_giver_status',
                           userId : userId,
                           goalId : goalId                           
                       },
                       dataType: "json",
                       success: function (result) {
                           alertify.success('Status changed for ' + goalName); 
                           IfThenGive_Goals_Table.api().ajax.reload();
                       }
                   });
                },
                function ()
                {
                    alertify.error('You Pressed Cancel');
                }); 
            });
    });
</script>
<?php }