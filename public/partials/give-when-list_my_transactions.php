<?php
/**
 *
 * Display Transactions's Data
 *
 * @class       AngellEYE_Give_When_Post_types
 * @version	1.0.0
 * @package	give-when
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_My_Transactions_Table {
    /**
     * Class Constructor
     * @since    1.0.0     
     */

    /**     * ***********************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     * ************************************************************************* */
    public function __construct() {       
    }

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        // add_action for the class
    }

    /**
     * Retrieve givers’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_transactions() {
        global $wpdb;
        $userID = get_current_user_id();
        
        
        $search   = $_POST['search']['value'];
        $start    = $_POST['start'];
        $length   = $_POST['length'];
        $filter   = 0;
        $colOrder = $_POST['order'][0]['column'];
    	$coldir   = $_POST['order'][0]['dir'];
        if($colOrder==0)
            $col='transactionId';
        else if($colOrder==1)
            $col='user_display_name';
        else if($colOrder==2)
            $col='amount';        
        else
            $col='transactionId';
        
        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'give_when_gec_email') as user_paypal_email,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              pm.meta_value as amount,
              b.meta_value as userId,
              c.meta_value as transactionId,
              t.meta_value as ppack,
              p.post_date as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'give_when_transactions_wp_user_id'
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'give_when_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'give_when_transactions_ack'
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id 
              WHERE  b.meta_value =".$userID;
        $sql .= ' group by  p.ID';
        if (isset($search)) {
            $sql .= "  Having (( PayPalPayerID LIKE '%{$search}%' ) OR ( user_paypal_email LIKE '%{$search}%' ) OR ( user_display_name LIKE '%{$search}%' ) OR ( amount LIKE '%{$search}%' ) OR ( transactionId LIKE '%{$search}%' ) OR ( ppack LIKE '%{$search}%' ) ) ";
        }
        if(isset($_REQUEST['payment_status-filter'])  && $_REQUEST['payment_status-filter'] != 'all' ){
          $sql .= "  Having (( ppack LIKE '{$_REQUEST['payment_status-filter']}' ) ) ";     
        }                    
        $sql .= "ORDER BY {$col} {$coldir} LIMIT {$start}, {$length}";
        
        if(isset($_REQUEST['records_show-filter'])){
            $per_page = $_REQUEST['records_show-filter'];
        }
        
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');
        
        return $result_array;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
        $userID = get_current_user_id();
        $search   = $_POST['search']['value'];        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'give_when_gec_email') as user_paypal_email,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              pm.meta_value as amount,
              b.meta_value as userId,
              c.meta_value as transactionId,
              t.meta_value as ppack,
              p.post_date as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'give_when_transactions_wp_user_id'
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'give_when_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'give_when_transactions_ack'    
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id
              WHERE  b.meta_value =".$userID;
        $sql .= ' group by  p.ID';
        if (isset($search)) {
            $sql .= "  Having (( PayPalPayerID LIKE '%{$search}%' ) OR ( user_paypal_email LIKE '%{$search}%' ) OR ( user_display_name LIKE '%{$search}%' ) OR ( amount LIKE '%{$search}%' ) OR ( transactionId LIKE '%{$search}%' ) OR ( ppack LIKE '%{$search}%' ) ) ";
        }
        if(isset($_REQUEST['payment_status-filter']) && $_REQUEST['payment_status-filter'] != 'all' ){
          $sql .= "  Having (( ppack LIKE '{$_REQUEST['payment_status-filter']}' ) ) ";     
        }
        $wpdb->get_results($sql, 'ARRAY_A');
        return $wpdb->num_rows;
    }


    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'transactionId':
                _e($item['transactionId']);
                break;
            case 'user_display_name':
                _e($item['user_display_name'],'angelleye_give_when');
                break;
            case 'amount' :
                $ccode = get_option('gw_currency_code');
                $paypal = new Give_When_PayPal_Helper();
                $symbol = $paypal->get_currency_symbol($ccode);
                _e($symbol.$item['amount'],'angelleye_give_when');
                break;
            case 'PayPalPayerID' :
                _e($item['PayPalPayerID'],'angelleye_give_when');
                break;
            case 'user_paypal_email' :
                _e($item['user_paypal_email'],'angelleye_give_when');
                break;
            case 'ppack' :
                _e($item['ppack'],'angelleye_give_when');
                break;
            case 'Txn_date' :
                _e(date('Y-m-d', strtotime($item['Txn_date'])),'angelleye_give_when');
                break;
        }
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
         
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('transactions_per_page', 10);
        $current_page = $this->get_pagenum();
        if(isset($_REQUEST['records_show-filter'])){
            $per_page = $_REQUEST['records_show-filter'];
        }
        $total_items = self::record_count();
        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);


        $this->items = self::get_transactions($per_page, $current_page);
        ?>
        <!-- for transection filter -->
        <script type="text/javascript">
          jQuery('.ewc-filter-cat').live('change', function(){
              var catFilter = jQuery(this).val();
              if( catFilter != '' ){                  
                  document.location.href = '<?php echo site_url('/givewhen-transaction?'.$_SERVER['QUERY_STRING']); ?>&payment_status-filter='+catFilter;
              }
          });
          jQuery('.ewc-filter-num').live('change', function(){
              var rsFilter = jQuery(this).val();
              if( rsFilter != '' ){                  
                  document.location.href = '<?php echo site_url('/givewhen-transaction?'.$_SERVER['QUERY_STRING']); ?>&records_show-filter='+rsFilter;
              }
          });
        </script>
        
        <?php
    }

    public function extra_tablenav($which) {
        global $wpdb, $testiURL, $tablename, $tablet;
        $move_on_url = '&view=ListTransactions&payment_status-filter=';
        $selected = "selected='selected'";
        $status_filter = !empty($_REQUEST['payment_status-filter']) ? $_REQUEST['payment_status-filter'] : '';
        $rs_filter = !empty($_REQUEST['records_show-filter']) ? $_REQUEST['records_show-filter'] : '';
        if ($which == "top") {
            ?>
            <div class="alignleft actions bulkactions">
                <select name="cat-filter" class="ewc-filter-cat">
                    <option value=""><?php _e('Filter by Payment Status','angelleye_give_when'); ?></option>
                    <option value="all"><?php _e('Show All','angelleye_give_when'); ?></option>
                    <option value="<?php echo $move_on_url; ?>Success" <?php if ($status_filter == "Success") {
                echo $selected;
            } ?>><?php _e('Success','angelleye_give_when'); ?></option>
                    <option value="<?php echo $move_on_url; ?>Failure" <?php if ($status_filter == "Failure") {
                echo $selected;
            } ?>><?php _e('Failure','angelleye_give_when'); ?></option>
                    <option value="<?php echo $move_on_url; ?>pending" <?php if ($status_filter == "pending") {
                echo $selected;
            } ?>><?php _e('Pending','angelleye_give_when'); ?></option>
                </select>                            
                <select name="number_of_trans" class="ewc-filter-num">
                    <option value=""><?php _e('Show Number of Records','angelleye_give_when'); ?></option>
                    <option value="10" <?php if($rs_filter === '10') { echo $selected; } ?>>10</option>
                    <option value="25" <?php if($rs_filter === '25') { echo $selected; } ?>>25</option>
                    <option value="50" <?php if($rs_filter === '50') { echo $selected; } ?>>50</option>
                    <option value="100" <?php if($rs_filter === '100') { echo $selected; } ?>>100</option>
                </select>
            </div>
            <?php
        }
        if ($which == "bottom") {
            //The code that goes after the table is there
        }
    }

}

AngellEYE_Give_When_My_Transactions_Table::init();
