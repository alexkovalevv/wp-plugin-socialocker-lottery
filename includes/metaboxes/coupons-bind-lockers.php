<?php
/**
 * The file contains a class to configure the metabox Basic Options.
 * 
 * Created via the Factory Metaboxes.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2013, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * The class to configure the metabox Basic Options.
 * 
 * @since 1.0.0
 */
class OnpSL_AL_CouponsBindLockersMetabox extends FactoryMetaboxes000_FormMetabox
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

     * Inherited from the class FactoryFormMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $scope = 'onpsl_al';
    
    /**
     * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').
     * 
     * @link http://codex.wordpress.org/Function_Reference/add_meta_box
     * Inherited from the class FactoryMetabox.
     * 
     * @since 1.0.0
     * @var string
     */
    public $priority = 'core';
    
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
	
    public $cssClass = 'factory-bootstrap-000 factory-fontawesome-000';

    public function __construct( $plugin ) {
        parent::__construct( $plugin );
        
        $this->title = __('Выберите соц. замок:', 'sociallocker');
    }
    
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
        global $lotteryAddon;
          
        $form->add(array(             
                'type' => 'list',
                'way' => 'checklist',
                'name' => 'list_lockers',
                'data' => $this->getLockersList(),                
                'title' => '',
                'hint' => __('Выберите замки в которых будет разыгран этот промокод','sociallocker')            
        ));       
    }
    
    /**
     * Replaces the 'blurring' overlap with 'transparence' in the free version.
     * 
     * @since 1.0.0
     * @param type $postId
     */
    public function onSavingForm( $postId ) {
       
    }
    
    public function getLockersList() {       
       $lockersList = array();       
       $posts = get_posts( array(
                'numberposts'     => -1,               
                'orderby'         => 'post_date',
                'order'           => 'DESC',             
                'post_type'       => 'social-locker',             
                'post_status'     => 'publish'
        ));       
        foreach($posts as $post){ 
            setup_postdata($post);
            $lockersList[] = array(
                $post->ID,
                $post->post_title
            );
        }
        wp_reset_postdata();         
        return $lockersList;
    }
}

//FactoryMetaboxes000::register('OnpSL_AL_CouponsBindLockersMetabox', $lotteryAddon);
FactoryMetaboxes000::registerFor( new OnpSL_AL_CouponsBindLockersMetabox( $lotteryAddon ), 'onp-pm-coupon', $lotteryAddon );
