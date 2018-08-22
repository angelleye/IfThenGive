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
            'singular' => __('transaction', 'ifthengive'), //singular name of the listed records
            'plural' => __('transactions', 'ifthengive'), //plural name of the listed records
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
             (SELECT usr.user_email from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as core_email,
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
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id AND p.post_title Like '%GoalID:".esc_sql($_REQUEST['post'])."%'     
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox')  ";

        $sql .= ' group by  p.ID';
        if (isset($_REQUEST['s'])) {
            $request_s = esc_sql($_REQUEST['s']);
            $sql .= "  Having (( PayPalPayerID LIKE '%{$request_s}%' ) OR ( user_paypal_email LIKE '%{$request_s}%' ) OR ( user_display_name LIKE '%{$request_s}%' ) OR ( amount LIKE '%{$request_s}%' ) OR ( transactionId LIKE '%{$request_s}%' ) OR ( ppack LIKE '%{$request_s}%' ) ) ";
        }
        if(isset($_REQUEST['payment_status-filter'])  && $_REQUEST['payment_status-filter'] != 'all' ){
          $sql .= "  Having (( ppack LIKE '".esc_sql($_REQUEST['payment_status-filter'])."' ) ) ";     
        }
        if (isset($_REQUEST['orderby'])) {
            if (!empty($_REQUEST['orderby'])) {
                $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            } else {
                /* by default we will add post time/post type time order by  */
                $sql .= ' ORDER BY Txn_date ';
            }
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            /* by default we will add post time/post type time order by  */
            $sql .= ' ORDER BY Txn_date ';
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' DESC';
        }
        if(isset($_REQUEST['records_show-filter'])){
            $per_page = esc_sql($_REQUEST['records_show-filter']);
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');
        
        return $result_array;
    }

    /**
     * get_all_failed_givers function fetches all the giver's transaction list of the
     * failed payment.     
     * @since 1.0.0
     * @access public
     */
    
    public static function get_all_failed_givers($post_id){
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usr.user_email from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as core_email,
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
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox')  ";
        $sql .= ' group by  p.ID';                
        $sql .= "  Having (( ppack LIKE 'Failure' ) ) ";        
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');

        return $result_array;
    }    
    
    /**
     * get_remaining_process_failed_givers function fetch the transactions list with the
     * failed payment but only those which are remaining in the current process.
     * @since 1.0.0
     * @access public
     */
    
    public static function get_remaining_process_failed_givers($post_id){
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usr.user_email from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as core_email,
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
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp
              join {$wpdb->prefix}postmeta as wpm 
              on wpm.post_id = tp.post_id 
              JOIN {$wpdb->prefix}postmeta AS tpm
              ON tpm.post_id = tp.post_id
              WHERE 
              tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND
              wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox' AND
              tpm.`meta_value` = '0' AND tpm.`meta_key` = 'itg_txn_pt_status'    
                      )  ";
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
             (SELECT usr.user_email from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as core_email,
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
              JOIN {$wpdb->prefix}posts p ON p.ID = pm.post_id AND p.post_title Like '%GoalID:".esc_sql($_REQUEST['post'])."%'     
              WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_transactions_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox')  ";
        $sql .= ' group by  p.ID';
        if (isset($_REQUEST['s'])) {
            $request_s = esc_sql($_REQUEST['s']);
            $sql .= "  Having (( PayPalPayerID LIKE '%{$request_s}%' ) OR ( user_paypal_email LIKE '%{$request_s}%' ) OR ( user_display_name LIKE '%{$request_s}%' ) OR ( amount LIKE '%{$request_s}%' ) OR ( transactionId LIKE '%{$request_s}%' ) OR ( ppack LIKE '%{$request_s}%' ) ) ";
        }
        if(isset($_REQUEST['payment_status-filter']) && $_REQUEST['payment_status-filter'] != 'all' ){
          $sql .= "  Having (( ppack LIKE '".esc_sql($_REQUEST['payment_status-filter'])."' ) ) ";     
        }
        $wpdb->get_results($sql, 'ARRAY_A');
        return $wpdb->num_rows;
    }
    
   /*
    *   While do transaction, we are adding transaction status to 1.
    *   We are resting here to 0 in this function so that means process for that is completed.
    *   and all givers are set to 0.
    *   @since 1.0.0
    *   @access public*   
    */
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
                        wpm.`meta_value` = '".$sandbox."' AND wpm.`meta_key` = 'itg_signup_in_sandbox'
                )
                GROUP BY
                p.ID";
        $result_array = $wpdb->get_results($sql, 'ARRAY_A');
        return $result_array;
    }

    /** Text displayed when no transaction's data is available */
    public function no_items() {
        _e('No Transactions avaliable.', 'ifthengive');
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
                _e('<a href="'.esc_url(admin_url("edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=".$_REQUEST['post']."&view=GetTransactionDetail&txn_id=".$item['transactionId'])).'">' . $item['transactionId'] . '</a>','ifthengive');
                break;
            case 'user_display_name':
                _e($item['user_display_name'],'ifthengive');
                break;
            case 'amount' :
                $ccode = get_option('itg_currency_code');
                $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
                $symbol = $paypal->get_currency_symbol($ccode);
                _e($symbol.number_format($item['amount'],2,'.', ''),'ifthengive');
                break;
            case 'email':
                 _e($item['core_email'],'ifthengive');
                break;
            case 'ppinfo' :
                echo '<a href="#" class="btn btn-info" '
                    . 'title="PayPal Details of '.$item['user_display_name'].'" '
                    . ' data-toggle="popover"'
                    . ' data-placement="top" '
                    . ' data-html="true" '
                    . ' data-content="<div><p><strong>PayPal Email: </strong><br>'.$item['user_paypal_email'].'</p><p><strong>PayPal PayerID: </strong>'.$item['PayPalPayerID'].'</p></div>">'
                    . 'See Details</a>';
                break;                        
            case 'ppack' :
                _e($item['ppack'],'ifthengive');
                break;
            case 'Txn_date' :
                _e(date('Y-m-d h:i:s', strtotime($item['Txn_date'])),'ifthengive');
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
//        return sprintf(
//                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['user_id']
//        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns() {
        $columns = [
            'transactionId' => __('Transaction ID', 'ifthengive'),
            'user_display_name' => __('Name', 'ifthengive'),
            'amount' => __('Amount', 'ifthengive'),
//            'user_paypal_email' => __('PayPal Email ID', 'ifthengive'),
//            'PayPalPayerID' => __('PayPal Payer ID', 'ifthengive'),
            'email' => __('Email', 'ifthengive'),
            'ppack' => __('Payment Status', 'ifthengive'),
            'Txn_date' => __('Payment Date', 'ifthengive'),
            'ppinfo' => __('PayPal Info', 'ifthengive'),
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
            'email' => array('core_email', true),
            'Txn_date' => array('Txn_date', true)
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
            $per_page = esc_sql($_REQUEST['records_show-filter']);
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

                wp_safe_redirect(esc_url(add_query_arg()));
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

            wp_safe_redirect(add_query_arg());
            exit;
        }
    }
    
    /*
     * extra_tablenav method add new navigation tab
     * we have added filter/select box there.
     * @since    1.0.0
     * 
     */
    
    public function extra_tablenav($which) {
        global $wpdb, $testiURL, $tablename, $tablet;        
        $selected = "selected='selected'";
        $status_filter = !empty($_REQUEST['payment_status-filter']) ? sanitize_text_field($_REQUEST['payment_status-filter']) : '';
        $rs_filter = !empty($_REQUEST['records_show-filter']) ? sanitize_text_field($_REQUEST['records_show-filter']) : '';
        if ($which == "top") {
            ?>
            <div class="alignleft actions bulkactions">
                <a style="margin-right: 5px;margin-bottom: 5px;" class="btn btn-info btn-sm pull-left" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals'; ?>">Back to Goals</a>
                <select name="cat-filter" class="ewc-filter-cat">
                    <option value=""><?php _e('Filter by Payment Status','ifthengive'); ?></option>
                    <option value="all"><?php _e('Show All','ifthengive'); ?></option>
                    <option value="Success" <?php if ($status_filter == "Success") {
                echo $selected;
            } ?>><?php _e('Success','ifthengive'); ?></option>
                    <option value="Failure" <?php if ($status_filter == "Failure") {
                echo $selected;
            } ?>><?php _e('Failure','ifthengive'); ?></option>
                    <option value="pending" <?php if ($status_filter == "pending") {
                echo $selected;
            } ?>><?php _e('Pending','ifthengive'); ?></option>
                </select>                            
                <select name="number_of_trans" class="ewc-filter-num">
                    <option value=""><?php _e('Show Number of Records','ifthengive'); ?></option>
                    <option value="10" <?php if($rs_filter === '10') { echo $selected; } ?>>10</option>
                    <option value="25" <?php if($rs_filter === '25') { echo $selected; } ?>>25</option>
                    <option value="50" <?php if($rs_filter === '50') { echo $selected; } ?>>50</option>
                    <option value="100" <?php if($rs_filter === '100') { echo $selected; } ?>>100</option>
                </select>
               <div class="hidden" id="div_goal_in_process">
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post='.$_REQUEST['post'].'&view=RetryFailedTransactions&process=continue_old')); ?>" class="btn btn-warning"><?php _e('Continue with remaning','ifthengive') ?></a>
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post='.$_REQUEST['post'].'&view=RetryFailedTransactions')); ?>" class="btn btn-primary"><?php _e('Start Over', 'ifthengive'); ?></a>
                </div>
                <?php
                $failed = $this->get_all_failed_givers($_REQUEST['post']);
                if(!empty($failed)){
                ?>
                <a class="btn btn-primary btn-sm" id="ifthengive_fun_retry" data-postid="<?php echo sanitize_key($_REQUEST['post']); ?>" data-redirectUrl="<?php echo admin_url('edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post='.$_REQUEST['post'].'&view=RetryFailedTransactions');?>" href=""><?php _e('Retry Failure Payments','ifthengive') ?></a>
                <?php } ?>
                
            </div>
            <?php
        }
        if ($which == "bottom") {
            //The code that goes after the table is there
        }
    }

}

AngellEYE_IfThenGive_Transactions_Table::init();
