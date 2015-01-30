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
class OnpSL_Al_Statistic extends FactoryPages000_AdminPage  {
 
    public $menuTitle = 'Выйгрыши';
    public $menuPostType = 'onp-pm-coupon';
    
    public $id = "statistic";
    
    public function __construct(Factory000_Plugin $plugin) {   
        parent::__construct($plugin);
        $this->menuTitle = __('Выйгрыши', 'sociallocker');
    }
  
    public function assets($scripts, $styles) {        
        //$this->styles->request('bootstrap.core', 'bootstrap');
    }
    
    /**
     * Shows one of the help pages.
     * 
     * @sinve 1.0.0
     * @return void
     */
    public function indexAction() {
        global $lotteryAddon;        
              
            require_once ONP_AL_PLUGIN_DIR . '/includes/statistic-list-table.php';
                      
            $statisticWinnings = new OnpPM_PaymentsTable();          
            $statisticWinnings->prepare_items();         
         ?>
        
        <div class="wrap factory-bootstrap-000 factory-fontawesome-000">
            <h2><?php _e('Таблица выйгрышей'); ?></h2>           
            <form id="onpsl-al-statistic-winnings" method="get">              
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />               
                <?php $statisticWinnings->display(); ?>
            </form>
        </div> 
        <?php  
        return;
    }     
}

FactoryPages000::register($lotteryAddon, 'OnpSL_Al_Statistic');
/*@mix:place*/