<?php
/**
 * The file contains a short help info.
 * 
 * @author Paul Kashtanoff <paul@byonepress.com>
 * @copyright (c) 2014, OnePress Ltd
 * 
 * @package core 
 * @since 1.0.0
 */

/**
 * Common Settings
 */
class OnpSL_Al_Settings extends FactoryPages000_AdminPage  {
 
    public $menuTitle = 'Настройки лотереи';
    public $menuPostType = 'onp-pm-coupon';
    
    public $id = "settings";
    
    public function __construct(Factory000_Plugin $plugin) {   
        parent::__construct($plugin);
        $this->menuTitle = __('Настройки лотереи');
    }
  
    public function assets($scripts, $styles) {
       $this->scripts->request('jquery');
       
       //$this->scripts->add(ONP_AL_PLUGIN_URL . "/assets/admin/js/addon.edit.js");
       //$this->styles->add(ONP_AL_PLUGIN_URL . "/assets/admin/css/addon.edit.css");
        
       $this->scripts->request( array( 
            'control.checkbox',
            'control.dropdown',
            ), 'bootstrap' );
        
       $this->styles->request( array( 
            'bootstrap.core', 
            'bootstrap.form-group',
            'bootstrap.separator',
            'control.dropdown',
            'control.checkbox'
            ), 'bootstrap' ); 
    }
    
    /**
     * Shows one of the help pages.
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function indexAction() {
            global $lotteryAddon;  
        
            $form = new FactoryForms000_Form(array(
                'scope' => 'onpsl_al',
                'name'  => 'advanced-setting'
            ), $lotteryAddon );

            $form->setProvider( new FactoryForms000_OptionsValueProvider(array(
                'scope' => 'onpsl_al'
            )));
           
            $formOptions[] = array(
                'type'      => 'wp-editor',
                'name'      => 'formRegisterHint',
                'title'     => __('Подсказка для формы регистрации', 'sociallocker'),
                'hint'      => __('Небольшая подсказка для формы регистрации в лотереи.', 'sociallocker'),   
                'tinymce'   => array(
                    'setup' => 'function(ed){ window.onpsl_al_coupons_edit.bindWpEditorChange( ed ); }',
                    'height' => 150
                ),
                'layout'    => array(
                    'hint-position' => 'left'
                )
            );

           $formOptions[] = array(
               'type' => 'separator'
           );
           
           $formOptions[] = array(
                'type'      => 'wp-editor',
                'name'      => 'infoEmailMessage',
                'title'     => __('Регистрация в лотереии', 'sociallocker'),
                'hint'      => __('Текст письма регистрации в лотереи', 'sociallocker'),   
                'tinymce'   => array(
                    'setup' => 'function(ed){ window.onpsl_al_coupons_edit.bindWpEditorChange( ed ); }',
                    'height' => 150
                ),
                'layout'    => array(
                    'hint-position' => 'left'
                )
            );

           $formOptions[] = array(
               'type' => 'separator'
           );
           
           $formOptions[] = array(
                'type'      => 'wp-editor',
                'name'      => 'prizeEmailMessage',
                'title'     => __('Получение выйгрыша', 'sociallocker'),
                'hint'      => __('Текст письма для получения выйгрыша', 'sociallocker'),   
                'tinymce'   => array(
                    'setup' => 'function(ed){ window.onpsl_al_coupons_edit.bindWpEditorChange( ed ); }',
                    'height' => 150
                ),
                'layout'    => array(
                    'hint-position' => 'left'
                )
            );
           
           $formOptions[] = array(
               'type' => 'separator'
           );
           
           $formOptions = apply_filters('onp_sl_advanced_settings', $formOptions );
           $form->add($formOptions);

           if ( isset( $_POST['save-action'] ) ) {
                $form->save();

                //$redirectArgs = apply_filters('onp_sl_settings_options_redirect_args', array('saved' => 1 ) );
                return $this->redirectToAction('index', array('saved' => 1 ));
         }
      
         ?>
         <div class="wrap ">
            <h2><?php _e('Global Settings', 'sociallocker') ?></h2>            
            
            <div class="factory-bootstrap-000">
            <form method="post" class="form-horizontal">

                <?php if ( isset( $_GET['saved'] ) ) { ?>
                <div id="message" class="alert alert-success">
                    <p>The settings have been updated successfully!</p>
                </div>
                <?php } ?>
                
                <?php do_action('onp_sl_settings_options_notices') ?>

                <div style="padding-top: 10px;">
                <?php $form->html(); ?>
                </div>
                
                <div class="form-group form-horizontal">
                    <label class="col-sm-2 control-label"> </label>
                    <div class="control-group controls col-sm-10">
                        <input name="save-action" class="btn btn-primary" type="submit" value="<?php _e('Save changes', 'sociallocker') ?>"/>
                    </div>
                </div>
            
            </form>
            </div>  
                
        </div>
        <?php  
        return;
    }     
}

FactoryPages000::register($lotteryAddon, 'OnpSL_Al_Settings');
/*@mix:place*/