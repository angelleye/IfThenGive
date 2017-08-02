<?php
/**
 *
 * Display Giver's Data
 *
 * @class       AngellEYE_Give_When_Post_types
 * @version		1.0.0
 * @package		give-when
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_Givers_Table extends WP_List_Table {
    
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
                'singular' => __( 'Giver', 'angelleye_give_when' ), //singular name of the listed records
                'plural'   => __( 'Givers', 'angelleye_give_when' ), //plural name of the listed records
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
    public static function get_givers( $per_page = 5, $page_number = 1 ) {       
        
        global $wpdb;        
        $sql = "SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'give_when_gec_billing_agreement_id') as BillingAgreement,
             um.meta_value As PayPalEmail,
             um.user_id,
             u.display_name as DisplayName,
             pm.meta_value as amount,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerID 
             FROM `{$wpdb->prefix}posts` as p 
             join `{$wpdb->prefix}users` as u on p.post_author = u.ID 
             join `{$wpdb->prefix}postmeta` as pm on pm.post_id = p.ID 
             left join {$wpdb->prefix}usermeta as um on um.user_id=u.ID 
             WHERE pm.`post_id` IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE `meta_value` = '{$_REQUEST['post']}' AND `meta_key` = 'give_when_signup_wp_goal_id') ";
        
             
            $sql .= " group by u.ID";
            if(isset($_REQUEST['s'])){
               $sql .= "  Having (( BillingAgreement LIKE '%{$_REQUEST['s']}%' ) OR ( u.display_name LIKE '%{$_REQUEST['s']}%' ) OR ( PayPalEmail LIKE '%{$_REQUEST['s']}%' ) OR ( amount LIKE '%{$_REQUEST['s']}%' ) OR ( PayPalPayerID LIKE '%{$_REQUEST['s']}%' )) ";               
            }
             if(isset($_REQUEST['orderby'])){
                 if(!empty($_REQUEST['orderby'])){
                    $sql .= ' ORDER BY '.$_REQUEST['orderby'];
                 }                 
                 else{
                     /* by default we will add post time/post type time order by  */
                     $sql .= ' ORDER BY PayPalEmail ';
                 }
                 $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
             }
             else{
                /* by default we will add post time/post type time order by  */
                $sql .= ' ORDER BY PayPalEmail ';
                $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
            }                    
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;    
        
        $result_array = $wpdb->get_results( $sql, 'ARRAY_A' );
        return $result_array;
    }
    
    public static function get_all_givers() {
        global $wpdb;        
        $sql = "SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'give_when_gec_billing_agreement_id') as BillingAgreement,
             um.meta_value As PayPalEmail,
             um.user_id,
             u.display_name as DisplayName,
             pm.meta_value as amount,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerID 
             FROM `{$wpdb->prefix}posts` as p 
             join `{$wpdb->prefix}users` as u on p.post_author = u.ID 
             join `{$wpdb->prefix}postmeta` as pm on pm.post_id = p.ID 
             left join {$wpdb->prefix}usermeta as um on um.user_id=u.ID 
             WHERE pm.`post_id` IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE `meta_value` = '{$_REQUEST['post']}' AND `meta_key` = 'give_when_signup_wp_goal_id') 
             group by u.ID";                    
             
        $result_array = $wpdb->get_results( $sql, 'ARRAY_A' );       
        return $result_array;
    }
    
    /**
    * Delete a customer record.
    *
    * @param int $id customer ID
    */
    public static function delete_customer( $id ) {
      global $wpdb;

      $wpdb->delete(
        "{$wpdb->prefix}customers",
        [ 'ID' => $id ],
        [ '%d' ]
      );
    }
    
    /**
    * Returns the count of records in the database.
    *
    * @return null|string
    */
    public static function record_count() {
      global $wpdb;

      $sql = "SELECT
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'give_when_gec_billing_agreement_id') as BillingAgreement,
             um.meta_value As PayPalEmail,
             um.user_id,
             u.display_name as DisplayName,
             pm.meta_value as amount,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = um.user_id and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerID 
             FROM `{$wpdb->prefix}posts` as p 
             join `{$wpdb->prefix}users` as u on p.post_author = u.ID 
             join `{$wpdb->prefix}postmeta` as pm on pm.post_id = p.ID 
             left join {$wpdb->prefix}usermeta as um on um.user_id=u.ID 
             WHERE pm.`post_id` IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE `meta_value` = '{$_REQUEST['post']}' AND `meta_key` = 'give_when_signup_wp_goal_id') ";
        $sql .= " group by u.ID";
        if(isset($_REQUEST['s'])){
           $sql .= "  Having (( BillingAgreement LIKE '%{$_REQUEST['s']}%' ) OR ( u.display_name LIKE '%{$_REQUEST['s']}%' ) OR ( PayPalEmail LIKE '%{$_REQUEST['s']}%' ) OR ( amount LIKE '%{$_REQUEST['s']}%' ) OR ( PayPalPayerID LIKE '%{$_REQUEST['s']}%' )) ";               
        }
     $wpdb->get_results( $sql, 'ARRAY_A' );     
     return $wpdb->num_rows;
    }
    
    /** Text displayed when no giver's data is available */
    public function no_items() {
      _e( 'No Givers avaliable.', 'angelleye_give_when' );
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
             _e($item['BillingAgreement'],'angelleye_give_when');
            break;
        case 'PayPalEmail':
            _e($item['PayPalEmail'],'angelleye_give_when');
            break;
        case 'amount' :
            $ccode = get_option('gw_currency_code');
            $paypal = new Give_When_PayPal_Helper();
            $symbol = $paypal->get_currency_symbol($ccode);
            _e($symbol.number_format($item['amount'],2),'angelleye_give_when');
            break;
        case 'PayPalPayerID' :
            _e($item['PayPalPayerID'],'angelleye_give_when');
            break;
        case 'DisplayName' :
            _e($item['DisplayName'],'angelleye_give_when');
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
        'BillingAgreement'=> __( 'Billing Agreement ID', 'angelleye_give_when' ),
        'DisplayName'    => __( 'Name', 'angelleye_give_when' ),
        'PayPalEmail'         => __( 'Givers', 'angelleye_give_when' ),
        'amount'       => __( 'Amount', 'angelleye_give_when' ),
        'PayPalPayerID' => __('PayPal Payer ID','angelleye_give_when')
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
        'PayPalEmail' => array( 'PayPalEmail', true ),        
        'amount' =>  array( 'amount', true ),
        'PayPalPayerID' => array( 'PayPalPayerID', true )
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

     $per_page     = $this->get_items_per_page( 'givers_per_page', 5 );     
     $current_page = $this->get_pagenum();
     
     $total_items  = self::record_count();       
     $this->set_pagination_args( [
       'total_items' => $total_items, //WE have to calculate the total number of items
       'per_page'    => $per_page //WE have to determine how many items to show on a page
     ] );


     $this->items = self::get_givers( $per_page, $current_page );
     
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

      wp_redirect( esc_url( add_query_arg() ) );
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

    wp_redirect( esc_url( add_query_arg() ) );
    exit;
  }
}
}

AngellEYE_Give_When_Givers_Table::init();