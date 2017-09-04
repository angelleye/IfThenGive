<?php
/**
 *
 * Display Goals's Data
 *
 * @class       AngellEYE_Give_When_My_Goals_Table
 * @version	1.0.0
 * @package	give-when
 * @category	Class
 * @author      Angell EYE <service@angelleye.com>
 */
class AngellEYE_Give_When_My_Goals_Table {
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
    public static function get_goals() {
        global $wpdb;
        $userID = get_current_user_id();
        
        
        $search   = $_POST['search']['value'];
        $start    = $_POST['start'];
        $length   = $_POST['length'];
        $filter   = 0;
        $colOrder = $_POST['order'][0]['column'];
    	$coldir   = $_POST['order'][0]['dir'];
        if($colOrder==0)
            $col='GoalName';
        else if($colOrder==1)
            $col='amount';
        else if($colOrder==2)
            $col='BillingAgreement';      
        else if($colOrder==3)
            $col='PayPalEmail';      
        else if($colOrder==4)
            $col='PayPalPayerId';      
        else if($colOrder==5)
            $col='post_date';              
        else
            $col='post_date';
        
        
        $sql = "SELECT
                t.meta_value AS user_Id,  
                e.meta_value as goal_id,
                e.post_id AS e_postId,
                t.post_id AS t_postId,
                p.post_title as GoalName,  
                (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = '".$userID."' and usrmeta.meta_key = 'give_when_gec_billing_agreement_id') as BillingAgreement,
                (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = '".$userID."' and usrmeta.meta_key = 'give_when_gec_email') as PayPalEmail,
                (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = '".$userID."' and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerId,  
                (SELECT p2.post_date FROM {$wpdb->prefix}posts as p2 where p2.ID = e.post_id) as post_date, 
                pm.meta_value as amount  
                FROM
                 {$wpdb->prefix}postmeta AS pm
                LEFT JOIN
                 {$wpdb->prefix}postmeta AS t ON t.post_id = pm.post_id AND t.meta_key = 'give_when_signup_wp_user_id'
                LEFT JOIN
                 {$wpdb->prefix}postmeta AS e ON e.post_id = pm.post_id AND e.meta_key = 'give_when_signup_wp_goal_id'  
                JOIN {$wpdb->prefix}posts as p on p.ID = e.meta_value  
                 WHERE
                  t.meta_value IS NOT NULL AND t.meta_value ='".$userID."'";                                  
        
        $sql .= ' GROUP BY  e.meta_value, t.meta_value';
        if (isset($search)) {
            $sql .= "  Having (( GoalName LIKE '%{$search}%' ) OR ( BillingAgreement LIKE '%{$search}%' ) OR ( PayPalEmail LIKE '%{$search}%' ) OR ( PayPalPayerId LIKE '%{$search}%' ) OR ( amount LIKE '%{$search}%' ) OR ( post_date LIKE '%{$search}%' ) ) ";
        }                            
        $sql .= "ORDER BY {$col} {$coldir} LIMIT {$start}, {$length}";
        
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
        $sql = "SELECT
                t.meta_value AS user_Id,  
                e.meta_value as goal_id,
                e.post_id AS e_postId,
                t.post_id AS t_postId,
                p.post_title as GoalName,  
                (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = '".$userID."' and usrmeta.meta_key = 'give_when_gec_billing_agreement_id') as BillingAgreement,
                (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = '".$userID."' and usrmeta.meta_key = 'give_when_gec_email') as PayPalEmail,
                (SELECT usrmeta.meta_value FROM {$wpdb->prefix}usermeta as usrmeta where usrmeta.user_id = '".$userID."' and usrmeta.meta_key = 'give_when_gec_payer_id') as PayPalPayerId,  
                (SELECT p2.post_date FROM {$wpdb->prefix}posts as p2 where p2.ID = e.post_id) as post_date, 
                pm.meta_value as amount  
                FROM
                 {$wpdb->prefix}postmeta AS pm
                LEFT JOIN
                 {$wpdb->prefix}postmeta AS t ON t.post_id = pm.post_id AND t.meta_key = 'give_when_signup_wp_user_id'
                LEFT JOIN
                 {$wpdb->prefix}postmeta AS e ON e.post_id = pm.post_id AND e.meta_key = 'give_when_signup_wp_goal_id'  
                JOIN {$wpdb->prefix}posts as p on p.ID = e.meta_value  
                 WHERE
                  t.meta_value IS NOT NULL AND t.meta_value ='".$userID."'";                                  
        
        $sql .= ' GROUP BY  e.meta_value, t.meta_value';
        if (isset($search)) {
            $sql .= "  Having (( GoalName LIKE '%{$search}%' ) OR ( BillingAgreement LIKE '%{$search}%' ) OR ( PayPalEmail LIKE '%{$search}%' ) OR ( PayPalPayerId LIKE '%{$search}%' ) OR ( amount LIKE '%{$search}%' ) OR ( post_date LIKE '%{$search}%' ) ) ";
        }
        $wpdb->get_results($sql, 'ARRAY_A');
        return $wpdb->num_rows;
    }

}

AngellEYE_Give_When_My_Goals_Table::init();
