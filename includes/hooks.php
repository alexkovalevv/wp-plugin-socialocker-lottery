<?php 

register_activation_hook( ONP_AL_MAIN_FILE, 'onpsl_al_install');

function onpsl_al_install() {
        global $wpdb;
               
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   
        
        $sql = "CREATE TABLE {$wpdb->prefix}al_users (
                    ID INT(11) NOT NULL AUTO_INCREMENT,
                    email VARCHAR(100) NOT NULL,
                    register_time INT(11) NOT NULL DEFAULT 0,
                    refererLockerId INT(11) NOT NULL DEFAULT 0,
                    status INT(1) NOT NULL DEFAULT 0,
                    PRIMARY KEY  (ID)                    
                );"; 
        dbDelta($sql);
        
        $sql = "CREATE TABLE {$wpdb->prefix}al_win_coupons (
                    ID INT(11) NOT NULL AUTO_INCREMENT,
                    products_id VARCHAR(255) NOT NULL,
                    coupon_type_id INT(11) NOT NULL,
                    user_id INT(11) NOT NULL,
                    register_time INT(11) NOT NULL,
                    expiration_time INT(11) NOT NULL, 
                    discount INT(11) NOT NULL DEFAULT 0,
                    code VARCHAR(20) NOT NULL,
                    status INT(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY  (ID)                                        
                );";  
        dbDelta($sql);
        
        add_option('onpsl_al_infoEmailMessage', 
                '<p>Через {lottery_expectation_days} дня вам на почту придет письмо с результатами лотереи,
                 лотерея беспроигрышная, вы получите скидку до 30% или лицензию на Социальный замок Wordpress Basic (jQuery)</p>'
         );
        add_option('onpsl_al_infoEmailMessage', 
               '<p>Здравствуйте!</p>
                <p>Поздравляем, Вы участвуете в беспроигрошной лотереи OnePress!</p>
                <p>&nbsp;</p>
                <p>По прошествии {lottery_expectation_days}х дней вам на почту придет письмо, с результатами выигрыша.</p>
                <p>Вы сможете выиграть скидку до 30% или лицензию на Социальный замок Basic (jQuery) .</p>
                <p>Если у вас возникли какие-либо вопросы, вы можете обратиться в нашу службу поддержки ответным письмом.</p>
                <p>&nbsp;</p>
                <p><strong>Внимание:</strong> Запрещено удалять записи со своей стены в социальных сетях, запрещено отписываться из групп. За нарушение правил, ваша скидка или лицензия аннулируется.</p>
                <p>&nbsp;</p>'
        );
        add_option('onpsl_al_prizeEmailMessage',
               '<p>Здравствуйте!</p>
                <p>Вы участвуете в беспроигрошной лотереи OnePress!</p>
                <p>&nbsp;</p>
                <p>Ваш выигрыш: {prize_title}</p>
                <p>Промокод: <strong>{coupon_code}</strong></p>
                <p>Используя данный промокод вы получите скидку: {discount}%</p>
                <p>Промокод должен быть использован до {coupon_expectation}, иначе он аннулируется.</p>
                <p>&nbsp;</p>
                <p><strong>Внимание:</strong> Запрещено удалять записи со своей стены в социальных сетях, запрещено отписываться из групп. За нарушение правил, ваша скидка или лицензия аннулируется.</p>'
        );        
}
       
add_action( 'wp_ajax_onpsl_al_register_lottery_email', 'onpsl_register_lottery_email' );
add_action( 'wp_ajax_nopriv_onpsl_al_register_lottery_email', 'onpsl_register_lottery_email' );

function onpsl_register_lottery_email() {        
        global $wpdb;
        
        if ( !isset($_POST['email']) ) {          
            echo json_encode( array('error' => 'Значения поля Email не существует!') );
            exit;
        }
        
        if ( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {      
            echo json_encode( array('error' => 'Некорректно введен Email адрес!') );
            exit;
        }
        
        include_once ONP_AL_PLUGIN_DIR.'/includes/classes/lottery.class.php'; 
        $lottery = new OnpSL_Lottery();        
        
        if( sizeof($lottery->getUserByEmail($_POST['email'])) ) {          
            echo json_encode( array('error' => 'Email адрес уже зарегистрирован!') );
            exit;
        }
       
        $wpdb->insert(
             $wpdb->prefix.'al_users',
             array( 
                 'email' => $wpdb->escape($_POST['email']),
                 'register_time' => time(),
                 'refererLockerId' => $_POST['lockerId'],
                 'status' => 0
             ),
             array( '%s', '%d', '%d' )
        );
        
        $lotteryExpectation = get_post_meta($_POST['lockerId'], 'sociallocker_lotteryConfirmation', true);
        
        if( !$lotteryExpectation ) {
            
            $coupon = $lottery->createCoupon($userInfo[0]->refererLockerId, $wpdb->insert_id);
            $lottery->updateUserStatus($email);             
            $lottery->sensEmailPrize($_POST['email'], $coupon);
            
            $prizeMessage = $lottery->filterVarMessage($lottery->getOption('prizeEmailMessage'),
                array(            
                    'discount'  => $coupon['discount'],
                    'prize_title' => $lottery->getCouponById($coupon['couponId'])->post_title,
                    'coupon_code' => $coupon['code'],
                    'coupon_expectation' => $lottery->getPostMeta($coupon['couponId'], 'finishDate')
                ), false
            );
            
            echo json_encode( array('success' => $prizeMessage) );
            exit;
        }
        
        $lottery->sendEmailInfo($_POST['email']);   
        
        $infoMessage = $this->filterVarMessage($this->getOption('infoEmailMessage'), 
            array(
                'lottery_expectation_days' => $lotteryExpectation
            ), false
        );
        
        echo json_encode( array('success' => $infoMessage) );        
        exit;
}

add_action( 'wp_enqueue_scripts', 'onpsl_al_enqueue_scripts' ); 

function onpsl_al_enqueue_scripts() {
    wp_register_style( 'lottery-box-css', ONP_AL_PLUGIN_URL . '/assets/css/addon.lottery-box.css' );
    wp_register_script( 'lottery-box-js', ONP_AL_PLUGIN_URL . '/assets/js/addon.lottery-box.js', array(), '1.0.0', true );    
}

add_action( 'onp_sl_preview_head', 'onpsl_al_begin_creater_script' ); 
add_action( 'onp_sl_begin_creater_script', 'onpsl_al_begin_creater_script' );

add_filter('onp_pm_coupon_basic_options_form', 'onpsl_al_coupon_basic_options');

function onpsl_al_coupon_basic_options($items) {
    
       foreach( $items as $key => $val ) {
           if( $val['name'] == 'discount' )
               unset($items[$key]);
       }          
        
       $items[] = array(
                    'type'      => 'checkbox',
                    'way'       => 'buttons',
                    'name'      => 'discountRange',
                    'title'     => __('Безпроигрышная лотерея', 'sociallocker'),
                    'hint'      => __('Скидка для этого промокода будет выбрана случайным образом из установленного диапазона значений', 'sociallocker'),
                    'default'   => false
                  );
       $items[] =  array(
                    'type'      => 'div', 
                    'class'     => 'onpsl-al-discount-range',
                    'items'     => array(
                        array(
                            'type'      => 'textbox',
                            'name'      => 'discountMin',
                            'title'     => __('Минимальная скидка', 'sociallocker'),
                            'hint'      => __('Минимальная скидка которую получает человек использующий промокод, введите значение в процентах(%)', 'sociallocker')                
                        ),
                        array(
                            'type'      => 'textbox',
                            'name'      => 'discountMax',
                            'title'     => __('Максимальная', 'sociallocker'),
                            'hint'      => __('Максимальная скидка которую получает человек использующий промокод, введите значение в процентах(%)', 'sociallocker')                
                        )   
                    ));
         $items[] = array(
                    'type'      => 'div', 
                    'class'     => 'onpsl-al-discount',
                    'items'     => array(
                        array(
                            'type'      => 'textbox',
                            'name'      => 'discount',
                            'title'     => __('Cкидка', 'sociallocker'),
                            'hint'      => __('Cкидка которую получает человек использующий промокод, введите значение в процентах(%)', 'sociallocker')                
                        ),
                        array(
                            'type'      => 'textbox',
                            'name'      => 'chance',
                            'title'     => __('Шанс выйгрыша', 'sociallocker'),
                            'hint'      => __('Установите шанс выйгрыша этого промокода в процентах(%).', 'sociallocker'),
                            'default'   => 5

                        )
                    ));
     return $items;
}

function onpsl_al_begin_creater_script() {
    ?>
     <script>
        (function($){
            if( !window.onpsl.al ) window.onpsl.al = {};
            window.onpsl.al.options = {ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>'};
            $(document).bind('onp-sl-trigger-state', function(e, data, $sl, api){
                if ( !$sl || !data.totalSteps ) return;

                var $state = $sl.find(".onp-sociallocker-steps-state");
                if ( !$state.length ) {
                    $state = $("<div class='onp-sociallocker-steps-state'></div>");
                    $sl.find(".onp-sociallocker-inner-wrap").append($state);
                }

                var label = "{0} из {1} шагов пройдено";
                label = label.replace("{0}", data.completedSteps);
                label = label.replace("{1}", data.totalSteps);

                $state.text(label);
            });
        })(jQuery);
     </script>
     <style>
        .onp-sociallocker-steps-state {
            text-align: center;
            padding: 15px 0 0 0;
            color: #fff;
            font-weight: bold;
        }
        .onp-sociallocker-flip.onp-sl-step-completed .onp-sociallocker-overlay-front {
            background: #7c741b!important;                
            color: #fff;
        }
        .onp-sociallocker-flat .onp-sociallocker-flip.onp-sl-step-completed .onp-sociallocker-overlay-front {
            border-bottom-color: #47420e!important; 
        }
     </style>
    <?php
}

add_filter( 'onp_sl_content_shortcode', 'onpsl_al_show_email_box', 10, 2 );   

function onpsl_al_show_email_box( $content, $lockerId ) {
    ob_start();
    wp_enqueue_style('lottery-box-css');
    wp_enqueue_script('lottery-box-js');
    ?>
     <div class="onp-al-emailbox" data-locker-id="<?php echo $lockerId; ?>">
         <p>Пожалуйста введите Ваш email:</p>
         <div class="onp-al-emailbox-form">
             <input type="text" id="onp-al-email"/>
             <a href="#" class="button button-small" id="onp-al-send-email">Отправить</a>
         </div>
         <div class="onp-al-emailbox-descriptions">
            <?php
               $lotteryExpectation = get_post_meta($lockerId, 'sociallocker_lotteryConfirmation', true);
               include_once ONP_AL_PLUGIN_DIR.'/includes/classes/lottery.class.php'; 
               $lottery = new OnpSL_Lottery();
                
               echo $lottery->filterVarMessage($lottery->getOption('formRegisterHint'), 
                       array(
                            'lottery_expectation_days' => $lotteryExpectation
                       ), false
                    );
            ?>            
         </div>
         <span class="onp-pm-error-container"></span>
         <div class="onp-al-emailbox-loader"></div>
     </div>
    <?php
    $out = ob_get_contents();
    ob_end_clean();
    return $content.$out;       
}
