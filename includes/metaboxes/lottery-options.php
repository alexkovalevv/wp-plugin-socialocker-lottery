<?php  
function onpsl_al_add_locker_options( $params, $id ) {
        $params['locker']['stepByStep'] = get_post_meta($id, 'sociallocker_stepByStep', false );
        return $params;
} 

add_filter( 'onp_sl_locker_options', 'onpsl_al_add_locker_options', 10, 2 );  


class OnpSL_AL_LotteryOptionsMetabox extends FactoryMetaboxes000_FormMetabox
{
    /**
     * A visible title of the metabox.
     * 
     * Inherited from the class FactoryMetabox.
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * 
     * @since 1.0.0
     * @var string
     */
    public $title;  
    
    
    /**
     * A prefix that will be used for names of input fields in the form.
     * 
     * Inherited from the class FactoryFormMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $scope = 'sociallocker';
    
    /**
     * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $priority = 'low';
    
    /**
     * The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side'). 
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $context = 'side';
    
    
    public function __construct( $plugin ) {
        parent::__construct( $plugin );
        
        $this->title = __('Настройки лотереи', 'sociallocker');
    }
    
    public $cssClass = 'factory-bootstrap-000';
    
    /**
     * Configures a form that will be inside the metabox.
     * 
     * @see FactoryMetaboxes000_FormMetabox
     * @since 1.0.0
     * 
     * @param FactoryForms000_Form $form A form object to configure.
     * @return void
     */ 
    public function form( $form ) {
        /*@mix:place*/
        
        $options = array(  
            
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'stepByStep',
                'title'     => __('Лотерея', 'sociallocker'),
                'hint'      => __('После активации лотереи, социальный замок перейдет в пошаговый режим', 'sociallocker'),
                'default'   => false
            ),
            
            array(
                'type'      => 'textbox',
                'name'      => 'lotteryConfirmation',
                'title'     => __('Задержка', 'sociallocker'),
                'hint'      => __('Задержка розыгрыша в днях, если установлен 0, розыгрышь проходит моментально.', 'sociallocker'),
                'icon'      => ONP_SL_PLUGIN_URL . '/assets/admin/img/timer-icon.png',
                'default'   => 3
            ),
            
            array(
                'type'      => 'checkbox',
                'way'       => 'buttons',
                'name'      => 'notifyEmail',
                'title'     => __('Уведомления', 'sociallocker'),
                'hint'      => __('Если Вкл, уведомляет вас о новых участниках и выйгрышах в лотереи', 'sociallocker'),
                'default'   => false
            )                      
        );  
        
        $options = apply_filters('onp_sl_advanced_options', $options);
        $form->add($options);
    }
    
}

//FactoryMetaboxes000::register( 'OnpSL_AL_LotteryOptionsMetabox', $lotteryAddon );
FactoryMetaboxes000::registerFor( new OnpSL_AL_LotteryOptionsMetabox( $lotteryAddon ), 'social-locker', $lotteryAddon );
