<?php
/**
 * IfThenGive Transaction template.
 *
 * This template can be overriden by copying this file to your-theme/IfThenGive/template-ifthengive-my-transactions.php
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
<div class="itg_hr-title itg_hr-long itg_center"><abbr><?php _e('Transactions', ITG_TEXT_DOMAIN) ?></abbr></div>
<div class="itg_center_container">   
    <div class="itgcontainer">
        <div class="itg_table-responsive">
            <table class="itg_table" id="IfThenGive_Transaction_Table" width="100%">
                <thead>
                    <tr>
                        <th><?php _e('Transaction ID', ITG_TEXT_DOMAIN); ?></th>                        
                        <th><?php _e('Amount', ITG_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Goal Name', ITG_TEXT_DOMAIN); ?></th>                                                
                        <th><?php _e('Payment Status', ITG_TEXT_DOMAIN); ?></th>
                        <th><?php _e('Payment Date', ITG_TEXT_DOMAIN); ?></th>
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
        var IfThenGive_Transaction_Table = $('#IfThenGive_Transaction_Table').dataTable({
            "serverSide": true,
            "responsive": true,
            "colReorder": true,
            "bRetrieve": true,
            "processing": true,
            "oLanguage": {"sEmptyTable": 'No Transactions Found', "sZeroRecords": 'No records Found'},
            "ajax": {
                url: "<?php echo admin_url('admin-ajax.php'); ?>?action=ifthengive_my_transactions",
                type: "POST"
            },
            "columnDefs": [
                {
                    "targets": [0], 'searchable': false,
                    "render": function (data, type, row) {
                        return row.transactionId;
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
                    "targets": [2],
                    "render": function (data, type, row) {
                        return row.goal_name;
                    }
                },
                {
                    "targets": [3],
                    "render": function (data, type, row) {
                        return row.ppack;
                    }
                },
                {
                    "targets": [4],
                    "render": function (data, type, row) {
                        return row.Txn_date;
                    }
                }
            ]
        });
    });
</script>
<?php }