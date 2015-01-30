<?php
/**
Plugin Name: {comp:lotteryAddon}
Plugin URI: {comp:pluginUrl}
Description: {comp:description}
Author: OnePress
Version: 1.0.0
Author URI: http://byoneress.com
*/

//error_reporting(E_ALL); 
//ini_set("display_errors", 1); 

define('ONP_AL_PLUGIN_DIR', dirname(__FILE__));
define('ONP_AL_PLUGIN_URL', plugins_url( null, __FILE__ ));
define('ONP_AL_MAIN_FILE', __FILE__);

function onpsl_al_init() {  
    //if( !defined('ONP_SL_PLUGIN_ACTIVE') ) return false; 
    
    include_once ONP_AL_PLUGIN_DIR.'/includes/classes/lottery.class.php';    
    $lottery = new OnpSL_Lottery(); 
}
add_action( 'init', 'onpsl_al_init' );

#comp remove
// the compiler library provides a set of functions like onp_build and onp_license 
// to check how the plugin work for diffrent builds on developer machines

require('libs/onepress/compiler/boot.php');
#endcomp

// creating a plugin via the factory
require('libs/factory/core/boot.php');
global $lotteryAddon;

$lotteryAddon = new Factory000_Plugin(__FILE__, array(
    'name'          => 'lottery-addon',
    'title'         => 'Lottery Add-on',
    'version'       => '1.0.0',
    'assembly'      => 'premium',
    'lang'          => 'ru_RU'
));  
 
// requires factory modules
$lotteryAddon->load(array(
    array( 'libs/factory/bootstrap', 'factory_bootstrap_000', 'admin' ),
    array( 'libs/factory/font-awesome', 'factory_fontawesome_000', 'admin' ),
    array( 'libs/factory/forms', 'factory_forms_000', 'admin' ),
    array( 'libs/factory/notices', 'factory_notices_000', 'admin' ),
    array( 'libs/factory/pages', 'factory_pages_000', 'admin' ),
    array( 'libs/factory/viewtables', 'factory_viewtables_000', 'admin' ),
    array( 'libs/factory/metaboxes', 'factory_metaboxes_000', 'admin' ),
    array( 'libs/factory/shortcodes', 'factory_shortcodes_000' ),
    array( 'libs/factory/types', 'factory_types_000' )  
));

include_once ONP_AL_PLUGIN_DIR.'/includes/hooks.php';

if ( is_admin() )
include_once ONP_AL_PLUGIN_DIR.'/admin/init.php';