<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class OnpPM_PaymentsTable extends WP_List_Table {
       
    
    function __construct(){
        global $status, $page;  
                
        //Set parent defaults
        parent::__construct( array(
            //'singular'  => 'pay_bid',     //singular name of the listed records
            'plural'    => 'onpsl-al-statistic-winnings',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }   
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
     
    function column_cb($item){
       return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
             'selection',  
             $item['id']
       );
    }    
    
    /*function column_created($item){
       
        $actions = array(
            'approve_payment'   => sprintf('<a href="' . get_admin_url() . '?page=%s&operation=approve&uid=%d&wid=%d">Одобрить</a>',$_REQUEST['page'],$item['userId'],$item['id']),
            'unapprove'    => sprintf('<a href="' . get_admin_url() . '?page=%s&operation=cancel&uid=%d&wid=%d">Отменить</a>',$_REQUEST['page'],$item['userId'],$item['id']),
        );
        if($item['paymentStatus'] == 'finish') 
            $actions = array();  
       
        return sprintf('%1$s %2$s',
             date('d.m.Y', $item['created']),          
             $this->row_actions($actions)
        );
    }*/
    
    function column_code($item){
        echo "<strong>" . $item['code'] . "</strong>"; 
    }
    
    function column_registerTime($item){
        echo date("Y-m-d", $item['register_time']); 
    }
    
    function column_userEmail($item){
        global $wpdb;
        
        $data = $wpdb->get_results("
             SELECT * FROM {$wpdb->prefix}al_users 
             WHERE ID = '" . $item['user_id'] . "'", ARRAY_A
        );      
        
        echo isset($data[0]['email']) ? $data[0]['email'] : null;
    }
    
    function column_coupon($item){            
        $coupon = get_post($item['coupon_type_id']);       
        $coupon_title = $coupon->post_title;        
        echo '<a href="' . get_admin_url() . 'post.php?post=' . $item['coupon_type_id'] . '&action=edit">' . $coupon_title . '</a>';      
    }
    
    function column_discount($item){
        global $wpdb;
        echo $item['discount'] . "%";        
    }
    
    function column_status($item){
        echo $item['status'] ? '<strong style="color:green">Использован</strong>' : '<strong style="color:orange">Не использован</strong>';;        
    }
       
    function single_row( $item ) {
        $usedCouponClass = !$item['status'] ? 'noused' : 'used' ;
       
        static $row_class = '';
        $row_class = ( $row_class == '' ? 'alternate ' : '' );
        
        echo '<tr class="' . $row_class . $usedCouponClass . '">';
        echo $this->single_row_columns( $item );
        echo '</tr>';
    }
    
    function get_columns(){
        $columns = array(
            'cb'            => '',
            'code'          => __('ID'),
            'registerTime'  => __('Дата'),
            'userEmail'     => __('Email'),
            'coupon'        => __('Промокод'),
            'discount'      => __('Скидка'),
            'status'        => __('Статус')           
        );
        return $columns;
    }   
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'created'     => array('date',false)   //true means it's already sorted            
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
          'delete'      => 'Удалить',
          'deactivate'  => 'Деактивировать',
          'activate'    => 'Активировать'
        );
        return $actions;
    }
       
    function prepare_items() {
        global $wpdb;

        $per_page = 25;
       
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
       
        $this->_column_headers = array($columns, $hidden, $sortable);           
        
        $current_page = $this->get_pagenum(); 
                
         
        /*if( isset($_GET['page']) && isset($_GET['operation']) && isset($_GET['uid']) && isset($_GET['wid'])) {             
             if($_GET['page'] == 'payments-table-onp-payments') {
                 require_once ONP_PM_PLUGIN_DIR . '/includes/classes/affiliates.php';
                 $affiliate = OnpPM_Affiliates::getCurrentAffiliate();
                        
                 switch($_GET['operation']){
                     case('approve'):
                        if( empty($_GET['uid']) && empty($_GET['wid']) ) return;
                        $response = $affiliate->approveWithdrawal($_GET['uid'], $_GET['wid']);

                        if ( is_wp_error( $response ) ) {                   
                             wp_die( $response->get_error_message() );
                        } 
                        wp_redirect(get_admin_url().'?page='.$_GET['page']);
                        exit; 
                     break;
                     case('cancel'):                        
                        if( empty($_GET['uid']) && empty($_GET['wid']) ) return;
                        $response = $affiliate->cancelWithdrawal($_GET['wid'], $_GET['uid']);

                        if ( is_wp_error( $response ) ) {                   
                             wp_die( $response->get_error_message() );
                        } 
                        wp_redirect(get_admin_url().'?page='.$_GET['page']);
                        exit;
                     break;
                 } 
            }
         }*/
        $order = isset($_GET['orderby']) && $_GET['orderby'] == 'date' && isset($_GET['order']) ? $_GET['order'] : 'DESC';
         
        $limit = "LIMIT " . ($current_page - 1) * $per_page  . ", " . $per_page;   
         
        $data = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}al_win_coupons ORDER BY register_time ".$order." ".$limit, ARRAY_A
        );            
        
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}al_win_coupons");       
        
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }
    
}
