<?php
/**
 * GiveWhenTransaction template.
 *
 * This template can be overriden by copying this file to your-theme/GiveWhen/givewhen-my-goals.php
 *
 * @author 	Angell EYE <andrew@angelleye.com>
 * @package 	Givewhen
 * @version     1.0.0
 */
if (!defined('ABSPATH'))
    exit; // Don't allow direct access
?>

<?php
if(! is_admin()){
?>
<div class="gw_hr-title gw_hr-long gw_center"><abbr><?php _e('GiveWhen Goals', 'givewhen') ?></abbr></div>
<div class="gw_center_container">   
    <div class="gwcontainer">
        <div class="gw_table-responsive">
            <table class="gw_table" id="GiveWhen_Goals_Table" width="100%">
                <thead>
                    <tr>
                        <th><?php _e('Goal Name', 'givewhen'); ?></th>                        
                        <th><?php _e('Amount', 'givewhen'); ?></th>                                           
                        <th><?php _e('Agreement Date', 'givewhen'); ?></th>
                    </tr>
                </thead>
            </table>            
        </div>            
    </div>
</div>
<?php
$ccode = get_option('gw_currency_code');
$paypal = new Give_When_PayPal_Helper();
$symbol = $paypal->get_currency_symbol($ccode);
?>
<script>
    jQuery(document).ready(function ($) {        
        var GiveWhen_Goals_Table = $('#GiveWhen_Goals_Table').dataTable({
            "serverSide": true,
            "responsive": true,
            "colReorder": true,
            "bRetrieve": true,
            "processing": true,
            "oLanguage": {"sEmptyTable": 'No Goals Found', "sZeroRecords": 'No records Found'},
            "ajax": {
                url: "<?php echo admin_url('admin-ajax.php'); ?>?action=givewhen_my_goals",
                type: "POST"
            },
            "columnDefs": [
                {
                    "targets": [0], 'searchable': false,
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
                    "targets": [2],
                    "render": function (data, type, row) {
                        return row.post_date;
                    }
                }                
            ]
        });       
    });
</script>
<?php }