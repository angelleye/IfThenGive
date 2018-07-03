<?php
/**
 *
 * Display Giver's Data
 *
 * @class       AngellEYE_IfThenGive_Givers_Table
 * @version		1.0.0
 * @package		ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_Givers_Table extends WP_List_Table {
    
    /**
     * Class Constructor
     * @since    1.0.0     
     */

    /**     * ***********************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     * ************************************************************************* */
    public function __construct() {
        
        parent::__construct( [
                'singular' => __( 'Giver', 'ifthengive' ), //singular name of the listed records
                'plural'   => __( 'Givers', 'ifthengive' ), //plural name of the listed records
                'ajax'     => false //should this table support ajax?

        ] );
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
    public static function get_givers( $per_page = 10, $page_number = 1 ) {       
                
        global $wpdb;        
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_gec_email') AS PayPalEmail,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_giver_".esc_sql($_REQUEST['post'])."_status') AS GiverStatus,
             um.user_id,
             u.user_email as CoreEmail,
             p.post_date as BADate,
             u.display_name as DisplayName,
             pm.`post_id` as signup_postid,
             pm.meta_value as amount,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID
             FROM `{$wpdb->prefix}posts` as p 
             join `{$wpdb->prefix}users` as u on p.post_author = u.ID 
             join `{$wpdb->prefix}postmeta` as pm on pm.post_id = p.ID 
             left join {$wpdb->prefix}usermeta as um on um.user_id=u.ID 
             WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_signup_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox') ";
        
             
            $sql .= " group by u.ID";
            if(isset($_REQUEST['s'])){
               $request_s = esc_sql($_REQUEST['s']);
               $sql .= "  Having (( BillingAgreement LIKE '%{$request_s}%' ) OR ( u.display_name LIKE '%{$request_s}%' ) OR ( PayPalEmail LIKE '%{$request_s}%' ) OR ( amount LIKE '%{$request_s}%' ) OR ( PayPalPayerID LIKE '%{$request_s}%' )) ";               
            }
             if(isset($_REQUEST['orderby'])){
                 if(!empty($_REQUEST['orderby'])){
                     $request_orderby = esc_sql($_REQUEST['orderby']);
                    $sql .= ' ORDER BY '.$request_orderby;
                 }                 
                 else{
                     /* by default we will add post time/post type time order by  */
                     $sql .= ' ORDER BY BADate ';
                 }
                 $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
             }
             else{
                /* by default we will add post time/post type time order by  */
                $sql .= ' ORDER BY BADate ';
                $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
            }
        if(isset($_REQUEST['records_show-filter'])){
            $per_page = esc_sql($_REQUEST['records_show-filter']);
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;            
        
        $result_array = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result_array;
    }
    
    public static function get_all_givers() {
        global $wpdb;       
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql = "SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_gec_email') AS PayPalEmail,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_giver_".esc_sql($_REQUEST['post'])."_status') AS GiverStatus,             
             um.user_id,
             u.user_email as CoreEmail,
             p.post_date as BADate,
             u.display_name as DisplayName,
             pm.meta_value as amount,
             pm.`post_id` as signup_postid,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID 
             FROM `{$wpdb->prefix}posts` as p 
             join `{$wpdb->prefix}users` as u on p.post_author = u.ID 
             join `{$wpdb->prefix}postmeta` as pm on pm.post_id = p.ID 
             left join {$wpdb->prefix}usermeta as um on um.user_id=u.ID 
             WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_signup_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox') 
             group by u.ID Having GiverStatus = 'active' OR GiverStatus IS NULL";
             
        $result_array = $wpdb->get_results( $sql, 'ARRAY_A' );               
        return $result_array;
    }
    
    
    public static function reset_givers_transaction_status($goal_id){
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql ="SELECT
                pm.`post_id` AS signup_postid                
                FROM
                    `{$wpdb->prefix}posts` AS p
                JOIN `{$wpdb->prefix}users` AS u
                ON
                    p.post_author = u.ID
                JOIN `{$wpdb->prefix}postmeta` AS pm
                ON
                    pm.post_id = p.ID
                LEFT JOIN {$wpdb->prefix}usermeta AS um
                ON
                    um.user_id = u.ID
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
                        tp.`meta_value` = '".esc_sql($goal_id)."' AND tp.`meta_key` = 'itg_signup_wp_goal_id' AND
                        wpm.`meta_value` = '".$sandbox."' AND wpm.`meta_key` = 'itg_signup_in_sandbox'
                )
                GROUP BY
                    u.ID";
        $result_array = $wpdb->get_results( $sql, 'ARRAY_A' );               
        return $result_array;
    }
    
    public static function get_remaining_process_givers($goal_id){
        global $wpdb;
        $sanbox_enable = get_option('itg_sandbox_enable');
        $sandbox = ($sanbox_enable === 'yes')  ? 'yes' : 'no';
        
        $sql ="SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_gec_email') AS PayPalEmail,             
             um.user_id,
             u.user_email as CoreEmail,
             p.post_date as BADate,
             u.display_name as DisplayName,
             pm.meta_value as amount,
             pm.`post_id` AS signup_postid,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_giver_".esc_sql($_REQUEST['post'])."_status') AS GiverStatus,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID 
                FROM
                    `{$wpdb->prefix}posts` AS p
                JOIN `{$wpdb->prefix}users` AS u
                ON
                    p.post_author = u.ID
                JOIN `{$wpdb->prefix}postmeta` AS pm
                ON
                    pm.post_id = p.ID
                LEFT JOIN {$wpdb->prefix}usermeta AS um
                ON
                    um.user_id = u.ID
                WHERE
                    pm.`post_id` IN(
                    SELECT
                        tp.post_id
                    FROM
                        {$wpdb->prefix}postmeta AS tp
                    JOIN {$wpdb->prefix}postmeta AS wpm
                    ON
                        wpm.post_id = tp.post_id
                    JOIN {$wpdb->prefix}postmeta AS tpm
                    ON
                        tpm.post_id = tp.post_id
                    WHERE
                        tp.`meta_value` = '".$goal_id."' AND tp.`meta_key` = 'itg_signup_wp_goal_id' AND
                        wpm.`meta_value` = '".$sandbox."' AND wpm.`meta_key` = 'itg_signup_in_sandbox' AND
                        tpm.`meta_value` = 0 AND tpm.`meta_key` = 'itg_transaction_status'
                )
                GROUP BY
                    u.ID Having GiverStatus = 'active' OR GiverStatus IS NULL";       
        $result_array = $wpdb->get_results( $sql, 'ARRAY_A' );               
        return $result_array;
    }
    
    
    /**
    * Delete a customer record.
    *
    * @param int $id customer ID
    */
    public static function delete_customer( $id ) {
//      global $wpdb;
//
//      $wpdb->delete(
//        "{$wpdb->prefix}customers",
//        [ 'ID' => $id ],
//        [ '%d' ]
//      );
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
        
      $sql = "SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_billing_agreement_id') as BillingAgreement,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_gec_email') AS PayPalEmail,
             (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta AS usrmeta WHERE usrmeta.user_id = um.user_id AND usrmeta.meta_key = 'itg_giver_".esc_sql($_REQUEST['post'])."_status') AS GiverStatus,
             um.user_id,
             u.user_email as CoreEmail,
             p.post_date as BADate,
             u.display_name as DisplayName,
             pm.meta_value as amount,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID 
             FROM `{$wpdb->prefix}posts` as p 
             join `{$wpdb->prefix}users` as u on p.post_author = u.ID 
             join `{$wpdb->prefix}postmeta` as pm on pm.post_id = p.ID 
             left join {$wpdb->prefix}usermeta as um on um.user_id=u.ID 
             WHERE pm.`post_id` IN (SELECT tp.post_id FROM {$wpdb->prefix}postmeta as tp  join {$wpdb->prefix}postmeta as wpm on wpm.post_id = tp.post_id WHERE tp.`meta_value` = '".esc_sql($_REQUEST['post'])."' AND tp.`meta_key` = 'itg_signup_wp_goal_id' AND wpm.`meta_value` = '".$sandbox."' ANd wpm.`meta_key` = 'itg_signup_in_sandbox') ";
        $sql .= " group by u.ID";
        if(isset($_REQUEST['s'])){
            $request_s = esc_sql($_REQUEST['s']);
           $sql .= "  Having (( BillingAgreement LIKE '%{$request_s}%' ) OR ( u.display_name LIKE '%{$request_s}%' ) OR ( PayPalEmail LIKE '%{$request_s}%' ) OR ( amount LIKE '%{$request_s}%' ) OR ( PayPalPayerID LIKE '%{$request_s}%' )) ";               
        }
     $wpdb->get_results( $sql, 'ARRAY_A' );     
     return $wpdb->num_rows;
    }
    
    public static function check_all_givers_suspended($goal_id){
        
    }

        /** Text displayed when no giver's data is available */
    public function no_items() {
      _e( 'No Givers avaliable.', 'ifthengive' );
    }
    
    /**
    * Method for name column
    *
    * @param array $item an array of DB data
    *
    * @return string
    */
    public function column_name( $item ) {
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
    public function column_default( $item, $column_name ) {        
      switch ( $column_name ) {
        case 'BillingAgreement':
             _e($item['BillingAgreement'],'ifthengive');
            break;
        case 'Status':
            $giverstatus = $item['GiverStatus'];
            /* if status is active we have to change it into suspended */
            if($giverstatus == 'active'){
                _e('Active','ifthengive');
            }
            /* if no status found,default is suspended */
            else if($giverstatus === NULL){
                _e('Active','ifthengive');
            }
            /* else status is always suspended so make it active */
            else{                
                _e('Suspend','ifthengive');
            }            
            break;
        case 'PayPalInfo':                
            echo '<a href="#" class="btn btn-info" '
                    . 'title="PayPal Details of '.$item['DisplayName'].'" '
                    . ' data-toggle="popover"'
                    . ' data-placement="top" '
                    . ' data-html="true" '
                    . ' data-content="<div><p><strong>PayPal Email: </strong><br>'.$item['PayPalEmail'].'</p><p><strong>PayPal PayerID: </strong>'.$item['PayPalPayerID'].'</p></div>">'
                    . 'See Details</a>';
            break;            
        case 'amount' :
            $ccode = get_option('itg_currency_code');
            $paypal = new AngellEYE_IfThenGive_PayPal_Helper();
            $symbol = $paypal->get_currency_symbol($ccode);
            echo $symbol.number_format($item['amount'],2,'.', '');
            break;        
        case 'DisplayName' :
            echo apply_filters('itg_givers_list_link','<a href="' . esc_url(site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals&page=ifthengive_givers&post=' . sanitize_key($_REQUEST['post']) . '&view=GetUsersTransactions&user_id=' . $item['user_id']) . '">' . esc_html($item['DisplayName']) . '</a>',$item['DisplayName'],$_REQUEST['post']);
            break;
         case 'BADate' :
            _e(date('Y-m-d',  strtotime($item['BADate'])),'ifthengive');
             break;
         case 'Email' :
            _e($item['CoreEmail'],'ifthengive');
             break;         
        case 'ITGAction' :
            if($item['BillingAgreement']==''){
                echo "-";
            }
            else{
                $giverstatus = $item['GiverStatus'];
                /* if status is active we have to change it into suspended */
                if($giverstatus == 'active'){               
                    $label = __('Suspend','ifthengive');                
                    $class = "btn-warning";
                }
                /* if no status found,default is suspended */
                else if($giverstatus === NULL){                
                    $label = __('Suspend','ifthengive');               
                    $class = "btn-warning";
                }
                /* else status is always suspended so make it active */
                else{                
                    $label = __('Activate','ifthengive');
                    $class = "btn-defalt";
                }            
                //echo apply_filters('itg_givers_action_link','<button type="button" class="btn '.$class.' btn-sm btn-cbaid" data-postid="'.$_REQUEST['post'].'" data-itgchangestatus="'.$label.'" data-userid="'.$item['user_id'].'">'.__($label,'ifthengive').'</button> ',$_REQUEST['post']);
                echo '<button type="button" class="btn btn-danger btn-sm btn-dgiver" data-signupid="'.$item['signup_postid'].'" data-goalid="'.sanitize_key($_REQUEST['post']).'" data-userid="'.$item['user_id'].'"><span></span>'.__(' Delete','ifthengive').'</button>';
            }
            break;
      }
    }
    
    function single_row( $item ) {
        $giverstatus = $item['GiverStatus'];
        if($giverstatus == 'active'){
            $class = "";
        }
        /* if no status found,default is suspended */
        else if($giverstatus === NULL){                            
            $class = "";
        }
        /* else status is always suspended so make it active */
        else{
            $class = "itg_suspended_row";
        }
        echo '<tr class="'.$class.'">';
        echo $this->single_row_columns( $item );
        echo "</tr>\n";
    }
    
    
    /**
    * Render the bulk edit checkbox
    *
    * @param array $item
    *
    * @return string
    */
    public function column_cb( $item ) {
//      return sprintf(
//        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['user_id']
//      );
    }
    
    
    /**
    *  Associative array of columns
    *
    * @return array
    */
    public function get_columns() {
      $columns = [
        //'cb'           => '<input type="checkbox" />',
        'BillingAgreement'=> __( 'Billing Agreement ID', 'ifthengive' ),
        'DisplayName'    => __( 'Name', 'ifthengive' ),
        'Email'         => __('Email','ifthengive'),         
        'amount'       => __( 'Amount', 'ifthengive' ),
        'PayPalInfo' => __('PayPal Info','ifthengive'),
        'BADate'       => __('Agreement Date','ifthengive'),
        'Status'       => __('Status','ifthengive'),
        'ITGAction' => __('Action','ifthengive')
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
        'BillingAgreement' => array( 'BillingAgreement', true ),
        'DisplayName' => array('DisplayName',true),
        'Email' => array('CoreEmail',true),
        'amount' =>  array( 'amount', true ),
        'BADate' => array('BADate', true)
      );

      return $sortable_columns;
    }
    
    /**
    * Returns an associative array containing the bulk action
    *
    * @return array
    */
    public function get_bulk_actions() {
//      $actions = [
//        'bulk-delete' => 'Delete'
//      ];
//
//      return $actions;
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

     $per_page     = $this->get_items_per_page( 'givers_per_page', 10 );     
     $current_page = $this->get_pagenum();
     if(isset($_REQUEST['records_show-filter'])){
            $per_page = esc_sql($_REQUEST['records_show-filter']);
     }
     $total_items  = self::record_count();       
     $this->set_pagination_args( [
       'total_items' => $total_items, //WE have to calculate the total number of items
       'per_page'    => $per_page //WE have to determine how many items to show on a page
     ] );


     $this->items = self::get_givers( $per_page, $current_page );
     ?>
        <!-- for transection filter -->
        <script type="text/javascript">          
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
  if ( 'delete' === $this->current_action() ) {

    // In our file that handles the request, verify the nonce.
    $nonce = esc_attr( $_REQUEST['_wpnonce'] );

    if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
      die( 'Go get a life script kiddies' );
    }
    else {
      self::delete_customer( absint( $_GET['customer'] ) );

      wp_safe_redirect(add_query_arg());
      exit;
    }

  }

  // If the delete bulk action is triggered
  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
       || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
  ) {

    $delete_ids = esc_sql( $_POST['bulk-delete'] );

    // loop over the array of record IDs and delete them
    foreach ( $delete_ids as $id ) {
      self::delete_customer( $id );

    }

    wp_safe_redirect(add_query_arg());
    exit;
  }
}
    public function extra_tablenav($which) {
        global $wpdb, $testiURL, $tablename, $tablet;
        $move_on_url = '&post=' . sanitize_key($_REQUEST['post']) . '&view=ListTransactions&payment_status-filter=';
        $selected = "selected='selected'";
        $status_filter = !empty($_REQUEST['payment_status-filter']) ? sanitize_text_field($_REQUEST['payment_status-filter']) : '';
        $rs_filter = !empty($_REQUEST['records_show-filter']) ? sanitize_text_field($_REQUEST['records_show-filter']) : '';
        if ($which == "top") {
            ?>
            <div class="alignleft actions bulkactions">                                
                <a style="margin-right: 5px;margin-bottom: 5px;" class="btn btn-info btn-sm pull-left" href="<?php echo site_url() . '/wp-admin/edit.php?post_type=ifthengive_goals'; ?>">Back to Goals</a>               
            </div>
            <select name="number_of_givers" class="ewc-filter-num">
                <option value=""><?php _e('Show Number of Records','ifthengive'); ?></option>
                <option value="10" <?php if($rs_filter === '10') { echo $selected; } ?>>10</option>
                <option value="25" <?php if($rs_filter === '25') { echo $selected; } ?>>25</option>
                <option value="50" <?php if($rs_filter === '50') { echo $selected; } ?>>50</option>
                <option value="100" <?php if($rs_filter === '100') { echo $selected; } ?>>100</option>
            </select>
            <?php
        }
        if ($which == "bottom") {
            //The code that goes after the table is there
        }
    }

}

AngellEYE_IfThenGive_Givers_Table::init();