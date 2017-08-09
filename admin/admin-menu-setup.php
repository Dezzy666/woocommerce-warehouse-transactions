<?php

/**
 * General settings for the plugin.
 *
 * @author      Jan Herzan
 * @category    Admin
 * @version     0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*****************************************************************************/
/*   SCREEN INIT                                                             */
/*****************************************************************************/

function wwt_setup_execution_menu() {
    add_menu_page(__('WWT', 'woocommerce_warehouse_transactions'), __('WWT', 'woocommerce_warehouse_transactions'), 'wwt_rights', 'wwt_executions', 'wwt_view_execution_page', plugin_dir_url( __FILE__ ) . '../images/main-icon.png', 82 );
    add_submenu_page( 'wwt_menu', __('Warehouse', 'woocommerce_warehouse_transactions'), __('Warehouse', 'woocommerce_warehouse_transactions'), 'wwt_rights', 'wwt_view_execution_page');
}
add_action( 'admin_menu', 'wwt_setup_execution_menu' );

function wwt_view_execution_page($param) {
    include 'main-page.php';
}
