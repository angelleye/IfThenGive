<?php
/**
 *
 * Display Transactions's Data
 *
 * @class       AngellEYE_IfThenGive_My_Transactions_Table
 * @version	1.0.0
 * @package	ifthengive
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_IfThenGive_My_Transactions_Table {
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
        
        
        $search   = esc_sql($_POST['search']['value']);
        $start    = esc_sql($_POST['start']);
        $length   = esc_sql($_POST['length']);
        $filter   = 0;
        $colOrder = esc_sql($_POST['order'][0]['column']);
    	$coldir   = esc_sql($_POST['order'][0]['dir']);
        if($colOrder==0)
            $col='transactionId';
        else if($colOrder==1)
            $col='amount';
        else if($colOrder==2)
            $col='goal_name';      
        else if($colOrder==3)
            $col='ppack';
        else if($colOrder==4)
            $col='ppack';        
        else
            $col='Txn_date';
        
        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              (SELECT pg.post_title from {$wpdb->prefix}posts pg where pg.ID = g.meta_value) AS goal_name,
              pm.meta_value as amount,
              b.meta_value as userId,
              c.meta_value as transactionId,
              t.meta_value as ppack,
              DATE_FORMAT(p.post_date,'%Y-%m-%d') as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'itg_transactions_wp_user_id'
              LEFT JOIN {$wpdb->prefix}postmeta g ON g.post_id = pm.post_id AND g.meta_key = 'itg_transactions_wp_goal_id'  
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'itg_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'itg_transactions_ack'
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
        $search   = esc_sql($_POST['search']['value']);        
        $sql = "SELECT  (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = b.meta_value and usrmeta.meta_key = 'itg_gec_payer_id') as PayPalPayerID,
             (SELECT usrmeta.meta_value from {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id =  b.meta_value and usrmeta.meta_key = 'itg_gec_email') as user_paypal_email,
             (SELECT usr.display_name from {$wpdb->prefix}users as usr where usr.ID =  b.meta_value ) as user_display_name,
              pm.post_id,
              pm.meta_value as amount,
              b.meta_value as userId,
              c.meta_value as transactionId,
              t.meta_value as ppack,
              DATE_FORMAT(p.post_date,'%Y-%m-%d') as Txn_date
              FROM `{$wpdb->prefix}postmeta` as pm 
              left JOIN {$wpdb->prefix}postmeta b ON b.post_id = pm.post_id AND b.meta_key = 'itg_transactions_wp_user_id'
              left JOIN {$wpdb->prefix}postmeta c ON c.post_id = pm.post_id AND c.meta_key = 'itg_transactions_transaction_id'
              left JOIN {$wpdb->prefix}postmeta t ON t.post_id = pm.post_id AND t.meta_key = 'itg_transactions_ack'    
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

}

AngellEYE_IfThenGive_My_Transactions_Table::init();
