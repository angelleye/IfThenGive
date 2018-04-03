<?php

/**
 *
 * Display Transactions's Data
 *
 * @class       AngellEYE_IfThenGive_Transactions_Table
 * @version	1.0.0
 * @package	ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Transactions_Table extends WP_List_Table {
    /**
     * Class Constructor
     * @since    1.0.0     
     */

    /**     * ***********************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     * ************************************************************************* */
    public function __construct() {

        parent::__construct([
            'singular' => __('Giver', ITG_TEXT_DOMAIN), //singular name of the listed records
            'plural' => __('Givers', ITG_TEXT_DOMAIN), //plural name of the listed records
            'ajax' => false //should this table support ajax?
        ]);
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
    public static function get_transactions($per_page = 5, $page_number = 1) {

        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';

        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              pm.meta_value as amount,
              b.meta_value as userId,
              c.meta_value as transactionId,
              t.meta_value as ppack,
              p.post_date as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'itg_transactions_wp_user_id'
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'itg_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'itg_transactions_ack'
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id AND p.post_title Like '%GoalID:{$_REQUEST['post']}%'     
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '{$_REQUEST['post']}' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'signup_in_sandbox')  ";

        $sql .= ' group by  p.ID';
        if (isset($_REQUEST['s'])) {
            $sql .= "  Having (( PayPalPayerID LIKE '%{$_REQUEST['s']}%' ) OR ( user_paypal_email LIKE '%{$_REQUEST['s']}%' ) OR ( user_display_name LIKE '%{$_REQUEST['s']}%' ) OR ( amount LIKE '%{$_REQUEST['s']}%' ) OR ( transactionId LIKE '%{$_REQUEST['s']}%' ) OR ( ppack LIKE '%{$_REQUEST['s']}%' ) ) ";
        }
        if(isset($_REQUEST['payment_status-filter'])  && $_REQUEST['payment_status-filter'] != 'all' ){
          $sql .= "  Having (( ppack LIKE '{$_REQUEST['payment_status-filter']}' ) ) ";     
        }
        if (isset($_REQUEST['orderby'])) {
            if (!empty($_REQUEST['orderby'])) {
                $sql .= ' ORDER BY ' . $_REQUEST['orderby'];
            } else {
                /* by default we will add post time/post type time order by  */
                $sql .= ' ORDER BY user_display_name ';
            }
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            /* by default we will add post time/post type time order by  */
            $sql .= ' ORDER BY user_display_name ';
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }
        if(isset($_REQUEST['records_show-filter'])){
            $per_page = $_REQUEST['records_show-filter'];
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');
        
        return $result_array;
    }

    public static function get_all_failed_givers($post_id){
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              pm.meta_value as amount,
              b.meta_value as user_id,
              c.meta_value as transactionId,
              t.meta_value as ppack,              
              p.post_date as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'itg_transactions_wp_user_id'
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'itg_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'itg_transactions_ack'
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id AND p.post_title Like '%GoalID:{$post_id}%'     
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '{$_REQUEST['post']}' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'signup_in_sandbox')  ";
        $sql .= ' group by  p.ID';                
        $sql .= "  Having (( ppack LIKE 'Failure' ) ) ";        
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');

        return $result_array;
    }    
    /**
     * Delete a txn record.
     *
     * @param int $id transaction ID
     */
    public static function delete_txn($id) {
//        global $wpdb;
//
//        $wpdb->delete(
//                "{$wpdb->prefix}customers", [ 'ID' => $id], [ '%d']
//        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              pm.meta_value as amount,
              b.meta_value as userId,
              c.meta_value as transactionId,
              t.meta_value as ppack,
              p.post_date as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'itg_transactions_wp_user_id'
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'itg_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'itg_transactions_ack'    
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id AND p.post_title Like '%GoalID:{$_REQUEST['post']}%'     
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '{$_REQUEST['post']}' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'signup_in_sandbox')  ";
        $sql .= ' group by  p.ID';
        if (isset($_REQUEST['s'])) {
            $sql .= "  Having (( PayPalPayerID LIKE '%{$_REQUEST['s']}%' ) OR ( user_paypal_email LIKE '%{$_REQUEST['s']}%' ) OR ( user_display_name LIKE '%{$_REQUEST['s']}%' ) OR ( amount LIKE '%{$_REQUEST['s']}%' ) OR ( transactionId LIKE '%{$_REQUEST['s']}%' ) OR ( ppack LIKE '%{$_REQUEST['s']}%' ) ) ";
        }
        if(isset($_REQUEST['payment_status-filter']) && $_REQUEST['payment_status-filter'] != 'all' ){
          $sql .= "  Having (( ppack LIKE '{$_REQUEST['payment_status-filter']}' ) ) ";     
        }
        $wpdb->get_results($sql, 'ARRAY_A');
        return $wpdb->num_rows;
    }
    
    public static function reset_transaction_status($goal_id=''){
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT
                pm.post_id as post_id
                FROM
                    `{$wpdb->prefix}postmeta` AS pm
                LEFT JOIN {$wpdb->prefix}postmeta b ON
                    b.post_id = pm.post_id AND b.meta_key = 'itg_transactions_wp_user_id'
                LEFT JOIN {$wpdb->prefix}postmeta c ON
                    c.post_id = pm.post_id AND c.meta_key = 'itg_transactions_transaction_id'
                LEFT JOIN {$wpdb->prefix}postmeta t ON
                    t.post_id = pm.post_id AND t.meta_key = 'itg_transactions_ack'
                JOIN {$wpdb->prefix}posts p ON
                    p.ID = pm.post_id AND p.post_title LIKE '%GoalID:".$goal_id."%'
                WHERE
                    pm.`post_id` IN(
                    SELECT
                        tp.post_id
                    FROM
                        {$wpdb->prefix}postmeta AS tp
                    JOIN {$wpdb->prefix}postmeta AS wpm
                    ON
                        wpm.post_id = tp.post_id
                    WHERE
                        tp.`meta_value` = '".$goal_id."' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND 
                        wpm.`meta_value` = '".$sandbox."' AND wpm.`meta_key` = 'signup_in_sandbox'
                )
                GROUP BY
                p.ID";
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');
        return $result_array;
    }

    /** Text displayed when no giver's data is available */
    public function no_items() {
        _e('No Transactions avaliable.', ITG_TEXT_DOMAIN);
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_name($item) {
//
//      // create a nonce
//      $delete_nonce = wp_create_nonce( 'sp_delete_customer' );
//
//      $title = '<strong>' . $item['name'] . '</strong>';
//
//      $actions = [
//        'delete' => sprintf( '<a href="#">Delete</a>')
//      ];
//
//      return $title . $this->row_actions( $actions );
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
                _e('<a href="' . site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=' . $_REQUEST['post'] . '&view=GetTransactionDetail&txn_id=' . $item['transactionId'] . '">' . $item['transactionId'] . '</a>',ITG_TEXT_DOMAIN);
                break;
            case 'user_display_name':
                _e($item['user_display_name'],ITG_TEXT_DOMAIN);
                break;
            case 'amount' :
                $ccode = get_option('itg_currency_code');
                $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
                $symbol = $paypal->get_currency_symbol($ccode);
                _e($symbol.number_format($item['amount'],2),ITG_TEXT_DOMAIN);
                break;
            case 'PayPalPayerID' :
                _e($item['PayPalPayerID'],ITG_TEXT_DOMAIN);
                break;
            case 'user_paypal_email' :
                _e($item['user_paypal_email'],ITG_TEXT_DOMAIN);
                break;
            case 'ppack' :
                _e($item['ppack'],ITG_TEXT_DOMAIN);
                break;
            case 'Txn_date' :
                _e(date('Y-m-d', strtotime($item['Txn_date'])),ITG_TEXT_DOMAIN);
                break;
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['user_id']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'transactionId' => __('Transaction ID', ITG_TEXT_DOMAIN),
            'user_display_name' => __('Name', ITG_TEXT_DOMAIN),
            'amount' => __('Amount', ITG_TEXT_DOMAIN),
            'user_paypal_email' => __('PayPal Email ID', ITG_TEXT_DOMAIN),
            'PayPalPayerID' => __('PayPal Payer ID', ITG_TEXT_DOMAIN),
            'ppack' => __('Payment Status', ITG_TEXT_DOMAIN),
            'Txn_date' => __('Payment Date', ITG_TEXT_DOMAIN)
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'transactionId' => array('transactionId', true),
            'user_display_name' => array('user_display_name', true),
            'amount' => array('amount', true),
            'user_paypal_email' => array('user_paypal_email', true),
            'PayPalPayerID' => array('PayPalPayerID', true)
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        /*$actions = [
            'bulk-retry' => 'Retry'
        ];

        return $actions;*/
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
                  <?php
                  if (isset($_REQUEST['payment_status-filter'])) {
                      $payment_status_url = remove_query_arg('payment_status-filter', admin_url('?' . $_SERVER['QUERY_STRING']));                      
                  } else {
                      $payment_status_url = admin_url('?' . $_SERVER['QUERY_STRING']);                      
                  }
                  ?>                              
                  document.location.href = '<?php echo $payment_status_url; ?>&payment_status-filter='+catFilter;
              }
          });
          jQuery('.ewc-filter-num').live('change', function(){
              var rsFilter = jQuery(this).val();
              if( rsFilter != '' ){
                  <?php
                  if (isset($_REQUEST['records_show-filter'])) {
                      $new_url = remove_query_arg('records_show-filter', admin_url('?' . $_SERVER['QUERY_STRING']));
                  } else {
                      $new_url = admin_url('?' . $_SERVER['QUERY_STRING']);
                  }
                  ?>
                  document.location.href = '<?php echo $new_url; ?>&records_show-filter='+rsFilter;
              }
          });
        </script>
        
        <?php
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'sp_delete_customer')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_customer(absint($_GET['customer']));

                wp_redirect(esc_url(add_query_arg()));
                exit;
            }
        }

        // If the delete bulk action is triggered
        if (( isset($_POST['action']) && $_POST['action'] == 'bulk-delete' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_customer($id);
            }

            wp_redirect(esc_url(add_query_arg()));
            exit;
        }
    }

    public function extra_tablenav($which) {
        global $wpdb, $testiURL, $tablename, $tablet;        
        $selected = "selected='selected'";
        $status_filter = !empty($_REQUEST['payment_status-filter']) ? $_REQUEST['payment_status-filter'] : '';
        $rs_filter = !empty($_REQUEST['records_show-filter']) ? $_REQUEST['records_show-filter'] : '';
        if ($which == "top") {
            ?>
            <div class="alignleft actions bulkactions">
                <a style="margin-right: 5px;margin-bottom: 5px;" class="btn btn-info btn-sm pull-left" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals'; ?>">Back to Goals</a>
                <select name="cat-filter" class="ewc-filter-cat">
                    <option value=""><?php _e('Filter by Payment Status',ITG_TEXT_DOMAIN); ?></option>
                    <option value="all"><?php _e('Show All',ITG_TEXT_DOMAIN); ?></option>
                    <option value="Success" <?php if ($status_filter == "Success") {
                echo $selected;
            } ?>><?php _e('Success',ITG_TEXT_DOMAIN); ?></option>
                    <option value="Failure" <?php if ($status_filter == "Failure") {
                echo $selected;
            } ?>><?php _e('Failure',ITG_TEXT_DOMAIN); ?></option>
                    <option value="pending" <?php if ($status_filter == "pending") {
                echo $selected;
            } ?>><?php _e('Pending',ITG_TEXT_DOMAIN); ?></option>
                </select>                            
                <select name="number_of_trans" class="ewc-filter-num">
                    <option value=""><?php _e('Show Number of Records',ITG_TEXT_DOMAIN); ?></option>
                    <option value="10" <?php if($rs_filter === '10') { echo $selected; } ?>>10</option>
                    <option value="25" <?php if($rs_filter === '25') { echo $selected; } ?>>25</option>
                    <option value="50" <?php if($rs_filter === '50') { echo $selected; } ?>>50</option>
                    <option value="100" <?php if($rs_filter === '100') { echo $selected; } ?>>100</option>
                </select>
                <a class="btn btn-primary btn-sm" href="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=<?php echo $_REQUEST['post']; ?>&view=RetryFailedTransactions"><?php _e('Retry Failure Payments',ITG_TEXT_DOMAIN) ?></a>
                
            </div>
            <?php
        }
        if ($which == "bottom") {
            //The code that goes after the table is there
        }
    }

}

AngellEYE_IfThenGive_Transactions_Table::init();
