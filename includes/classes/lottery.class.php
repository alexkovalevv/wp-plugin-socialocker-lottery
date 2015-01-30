<?php 
class OnpSL_Lottery {     
    
    public function createCoupon($lockerId, $userId) {
        global $wpdb;
        
        $code = $this->generateCouponCode();
        $coupons = $this->getCouponsByLocker($lockerId);
        $prize = $this->playCoupons($coupons);
        $expiration = $this->getPostMeta($prize['couponId'], 'finishDate');
        $poducts = json_encode($this->getProductsByCouponId($prize['couponId']));
                
        $wpdb->insert(
            $wpdb->prefix.'al_win_coupons',
            array(
                'products_id' => $poducts,
                'user_id' => $userId,
                'coupon_type_id' => $prize['couponId'],
                'register_time' => time(),
                'expiration_time' => strtotime($expiration),
                'discount' => $prize['discount'],
                'code' => $code,
                'status' => 0
            ),
            array( '%s', '%d', '%d' )
        );
        return array(
            'code'       => $code,
            'discount'   => $prize['discount'],
            'couponId'   => $prize['couponId']
        );
    }
        
    public function generateCouponCode() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return strtoupper(implode($pass));
    }       
            
    public function playCoupons($coupons) {
       $play = array();
       
       foreach( $coupons as $coupon ) {
          $isWinWin = $this->getPostMeta($coupon->ID, 'discountRange');
          $play[ $isWinWin ? 'winwin' : 'occasionally'][] = $coupon->ID;           
       }
      
       if( isset($play['occasionally']) ) {
            $drum = array();
            $rkeyCoupons = 0;        
            if( sizeof($play['occasionally']) > 1 ) {            
                $rkeyCoupons = array_rand($play['occasionally'], 1);  
            }
            $chanceWin = $this->getPostMeta($play['occasionally'][$rkeyCoupons], 'chance');
            
            for( $i = 0; $i <= 100; $i++ ) {
                 $drum[] = ( $i > ( 100 - $chanceWin ) ) ? 1 : 0;                
            }
            
            $rkeyDrum = array_rand($drum, 1);       
            if( $drum[$rkeyDrum] ) 
                return array(
                    'couponId' => $play['occasionally'][$rkeyCoupons],
                    'discount' => 100
                );
       }
       
       if( isset($play['winwin']) ) {           
            $rkeyCoupons = 0; 
            if( sizeof($play['winwin']) > 1 ) {
               $rkeyCoupons = array_rand($play['winwin'], 1);  
            }

            $rangeMin = $this->getPostMeta($play['winwin'][$rkeyCoupons], 'discountMin');
            $rangeMax = $this->getPostMeta($play['winwin'][$rkeyCoupons], 'discountMax');

            return array(
                    'couponId' => $play['winwin'][$rkeyCoupons],
                    'discount' => $this->generateNumberFormRange($rangeMin, $rangeMax)
                );
       }
    }
            
    public function generateNumberFormRange($min, $max) {
        $ints = array();
        for( $i = $min; $i < $max; $i++ ) {
            $ints[] = $i; 
        }
        $rand_key = array_rand($ints, 1);
        return $ints[$rand_key];
    }
    
    public function getCouponsByLocker($lockerId) {
        global $wpdb;
        
        $args = array(
            'meta_key' => 'onpsl_al_list_lockers',
            'meta_value' => $lockerId,
            'post_type' => 'onp-pm-coupon',
            'posts_per_page' => -1
        );
        return get_posts($args);
    }
    
    public function getCouponById($coupon_id) {
        return get_post($coupon_id); 
    }
    
    public function getProductsByCouponId($couponId) {
       global $wpdb; 
       $products = array();
       
       $result = $wpdb->get_results("
            SELECT meta_value FROM {$wpdb->prefix}postmeta
            WHERE post_id = '{$couponId}' and meta_key = 'onpsl_al_list_products'  
        " . $afterPeriod, ARRAY_N );
            
       foreach( $result as $val ) {
           $products[] = $val[0];
       }
       return $products;
    }
          
    public function getUsersAfterPeriod($period) {
        global $wpdb;
        
        $afterPeriod = $period ? " and register_time < " . time() . " - 86400 * " . $period : "";
        
        return $wpdb->get_results("
            SELECT*FROM {$wpdb->prefix}al_users
            WHERE status = '0'  
        " . $afterPeriod );
    }
    
    public function getUserByEmail($email) {
        global $wpdb;
        
        return $wpdb->get_results("
            SELECT*FROM {$wpdb->prefix}al_users
            WHERE email = '" . $wpdb->escape($email) . "'
        ");
    }
    
    public function getOption($optionName, $default = false) {
        
        if( empty($optionName) ) return false;
        $option = get_option('onpsl_al_'.$optionName, $default);
        
        return $option;
    }
    
    public function getPostMeta($post_id, $optionName) {
        
        if( empty($optionName) ) return false;
        $option = get_post_meta($post_id, 'onpsl_al_'.$optionName, true);
        
        return $option;
    }
    
    public function setOption( $name, $value ) {
        update_user_meta($this->id, 'onpsl_al_' . $name, $value);
    }
    
    public function updateUserStatus($email) {
       global $wpdb;
       
       $wpdb->update( $wpdb->prefix.'al_users',
                array( 'status' => 1 ),
                array( 'email' => $email ),
                array( '%d' ),
                array( '%s' )
        );
    }
    
   public function sendEmailInfo($email) {
              
        $n = "\n";        
        $subject  = __("Регистрация в лотереии OnePress!");
       
        $message = $this->getOption('infoEmailMessage');
        $message = $this->filterVarMessage($message, array());
        
        $headers = 'From: Социальный Замок <support@sociallocker.ru>' . "\r\n";
                
        wp_mail($email, $subject, $message, $headers );            
    }
    
    public function sensEmailPrize($email, $resultLottery) {
        global $wpdb;
                  
        $n = "\n";        
        $subject  = __("Получите свой выигрышь от OnePress!");
      
        $message = $this->getOption('prizeEmailMessage');        
        $message = $this->filterVarMessage($message,
            array(            
                'discount'  => $coupon['discount'],
                'prize_title' => $this->getCouponById($resultLottery['couponId'])->post_title,
                'coupon_code' => $resultLottery['code'],
                'coupon_expectation' => $this->getPostMeta($resultLottery['couponId'], 'finishDate')
            )
        );
        
        $headers = 'From: Социальный Замок <support@sociallocker.ru>' . $n;
                
        wp_mail($email, $subject, $message, $headers );
    }
    
    public function filterVarMessage($content, $arg, $clear = true) {        
       
       if( $clear ) $content = $this->clearMessage($content);  
       
       if( !sizeof($arg) )
           return $content;
       
       foreach( $arg as $key => $val ) {
           $content = str_replace('{' . $key . '}', $val, $content);
       }        
       return $content;
    }
    
    public function clearMessage($content) {
       $content = str_replace("</p>", "\n", $content);
       $content = str_replace("&nbsp;", "", $content);       
       $content = strip_tags($content);        
       return $content;
    }    
}