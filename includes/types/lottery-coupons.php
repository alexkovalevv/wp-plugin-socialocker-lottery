<?php
/**
 * Social Locker Type
 * Declaration for custom post type of Social Locler.
 * @link http://codex.wordpress.org/Post_Types
 */
class OnpSL_AL_LotteryAddonTypes extends FactoryTypes000_Type {
    
    /**
     * Custom post name.
     * @var string 
     */
    public $name = 'lottery-coupons';
    
    /**
     * Singular title for labels of the type in the admin panel.
     * @var string 
     */
    public $singularTitle = 'Лотерея';
    
    /**
     * Plural title for labels of the type in the admin panel.
     * @var string 
     */
    public $pluralTitle = 'Лотерея';
    
    
    /**
     * Template that defines a set of type options.
     * Allowed values: public, private, internal.
     * @var string 
     */
    public $template = 'private';
    
    /**
     * Capabilities for roles that have access to manage the type.
     * @link http://codex.wordpress.org/Roles_and_Capabilities
     * @var array 
     */
    public $capabilities = array('administrator');
        
    function __construct($plugin) {
        parent::__construct($plugin);
        
        $this->pluralTitle = __('Лотерея', 'sociallocker');
        $this->singularTitle = __('Лотерея', 'sociallocker');
    }
    
    /**
     * Type configurator.
     */
    public function configure() {
        global $lotteryAddon;
        
        /**
         * Labels
         */
        
        $pluralName = $this->pluralTitle;
        $singularName = $this->singularTitle;

        $labels = array(
            'singular_name' => $this->singularTitle,
            'name' => $this->pluralTitle,          
            'all_items' => sprintf( __('Промокоды', 'sociallocker'), $pluralName ),
            'add_new' => sprintf( __('+ Новый промокод', 'sociallocker'), $singularName ),
            'add_new_item' => sprintf( __('Добавить новый', 'sociallocker'), $singularName ),
            'edit' => sprintf( __('Редактировать', 'sociallocker') ),
            'edit_item' => sprintf( __('Редактировать промокод', 'sociallocker'), $singularName ),
            'new_item' => sprintf( __('Новый промокод', 'sociallocker'), $singularName ),
            'view' => sprintf( __('Просмотреть', 'factory') ),
            'view_item' => sprintf( __('Просмотреть промокод', 'sociallocker'), $singularName ),
            'search_items' => sprintf( __('Найти промокоды', 'sociallocker'), $pluralName ),
            'not_found' => sprintf( __('Промокоды не найдены', 'sociallocker'), $pluralName ),
            'not_found_in_trash' => sprintf( __('В корзине ничего не найдено', 'sociallocker'), $pluralName ),
            'parent' => sprintf( __('Родительский промокод', 'sociallocker'), $pluralName )
        );

        $this->options['labels'] = $labels;
        
        /**
         * Menu
         */

        $this->menu->title = __('Лотерея', 'sociallocker');
        $this->menu->icon = ONP_AL_PLUGIN_URL . '/assets/admin/img/menu-icon.png';           
        
        
        /**
         * Metaboxes
         */
         $this->metaboxes[] = "OnpSL_AL_CouponsBasicOptionsMetabox";
         $this->metaboxes[] = "OnpSL_AL_CouponsBindLockersMetabox";
         $this->metaboxes[] = "OnpSL_AL_CouponsBindProductsMetabox";  
     
        
        /**
         * View table
         */
        
        //$this->viewTable = new SocialLockerViewTable( $lotteryAddon );
        
        /**
         * Scripts & styles
        */  
        
        $this->scripts->request( array( 'jquery', 'jquery-effects-highlight' ) );
        
        $this->scripts->request( array( 
            'bootstrap.transition',
            'bootstrap.tab',
            'holder.more-link',
            'control.checkbox',
            'control.list',
            'control.dropdown',
            'bootstrap.modal',
            'bootstrap.datepicker'
            ), 'bootstrap' );
        
        $this->styles->request( array( 
            'bootstrap.core', 
            'bootstrap.form-group', 
            'bootstrap.form-metabox', 
            'bootstrap.tab', 
            'bootstrap.wp-editor', 
            'bootstrap.separator',
            'control.checkbox',
            'control.list',
            'control.dropdown',
            'holder.more-link',
            'bootstrap.datepicker'
            ), 'bootstrap' ); 
        
        do_action( 'onp_sl_sociallocker_type_assets', $this->scripts, $this->styles );   
    }
}

FactoryTypes000::register('OnpSL_AL_LotteryAddonTypes', $lotteryAddon);