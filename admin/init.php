<?php
include_once ONP_AL_PLUGIN_DIR.'/includes/metaboxes/lottery-options.php';
//include_once ONP_AL_PLUGIN_DIR.'/includes/metaboxes/coupons-basic-options.php';
include_once ONP_AL_PLUGIN_DIR.'/includes/metaboxes/coupons-bind-lockers.php';
//include_once ONP_AL_PLUGIN_DIR.'/includes/metaboxes/coupons-bind-products.php';

//include_once ONP_AL_PLUGIN_DIR.'/includes/types/lottery-coupons.php';

include_once ONP_AL_PLUGIN_DIR.'/admin/pages/statistic.php';
include_once ONP_AL_PLUGIN_DIR.'/admin/pages/settings.php';
    
function onpsl_al_enqueue_admin_scripts($hook) {    
    //if( !in_array($hook, array('post-new.php', 'post.php')) )  return;
    
    wp_enqueue_style( 'addon-edit', ONP_AL_PLUGIN_URL . "/assets/admin/css/addon.edit.css" );
    wp_enqueue_script( 'addon-edit-js', ONP_AL_PLUGIN_URL . "/assets/admin/js/addon.edit.js" );
}

add_action( 'admin_enqueue_scripts', 'onpsl_al_enqueue_admin_scripts' );
