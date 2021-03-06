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
    add_menu_page(__('WWT', 'woocommerce-warehouse-transactions'), __('WWT', 'woocommerce-warehouse-transactions'), 'wwt_rights', 'wwt_menu', 'wwt_view_execution_page', plugin_dir_url( __FILE__ ) . '../images/main-icon.png', 82 );
    add_submenu_page( 'wwt_menu', __('Warehouse', 'woocommerce-warehouse-transactions'), __('Warehouse', 'woocommerce-warehouse-transactions'), 'wwt_rights', 'wwt_menu');
    add_submenu_page( 'wwt_menu', __('Consignment Warehouses', 'woocommerce-warehouse-transactions'), __('Consignment Warehouses', 'woocommerce-warehouse-transactions'), 'wwt_rights', 'consignment_stock_management', 'wwt_view_consignment_stock_management');
    add_submenu_page( 'wwt_menu', __('Material', 'woocommerce-warehouse-transactions'), __('Material', 'woocommerce-warehouse-transactions'), 'wwt_rights', 'material_log_page', 'wwt_view_material_log_page');
    add_submenu_page( 'wwt_menu', __('Stock overview', 'woocommerce-warehouse-transactions'), __('Stock overview', 'woocommerce-warehouse-transactions'), 'wwt_rights', 'stock_overview_page', 'wwt_view_stock_overview_page');
    add_submenu_page( 'wwt_menu', __('Recepies overview', 'woocommerce-warehouse-transactions'), __('Recepies overview', 'woocommerce-warehouse-transactions'), 'wwt_recepies_rights', 'recepies_page', 'wwt_view_recepies_page');
    add_submenu_page( 'wwt_menu', __('Material overview', 'woocommerce-warehouse-transactions'), __('Material overview', 'woocommerce-warehouse-transactions'), 'wwt_recepies_rights', 'material_page', 'wwt_view_material_page');
    add_submenu_page( 'wwt_menu', __('Consignment stock management', 'woocommerce-warehouse-transactions'), __('Consignment stock management', 'woocommerce-warehouse-transactions'), 'manage_options', 'consignment_management', 'wwt_view_consignment_management');

    add_options_page( __('WWT settings', 'woocommerce-warehouse-transactions'), __('WWT settings', 'woocommerce-warehouse-transactions'), 'manage_options', 'woocommerce-warehouse-transactions', 'wwt_view_settings_page');
}
add_action( 'admin_menu', 'wwt_setup_execution_menu' );

function wwt_view_consignment_management($param) {
    include 'consignment-management.php';
    include 'admin-page-styles.php';
}

function wwt_view_execution_page($param) {
    include 'main-page.php';
    include 'admin-page-styles.php';
}

function wwt_view_settings_page($param) {
    include 'settings.php';
}

function wwt_view_recepies_page($param) {
    include 'recepies-overview.php';
    include 'admin-page-styles.php';
}

function wwt_view_material_page($param) {
    include 'material-overview.php';
    include 'admin-page-styles.php';
}

function wwt_view_material_log_page($param) {
    include 'material-log.php';
    include 'admin-page-styles.php';
}

function wwt_view_stock_overview_page($param) {
    include 'stock-overview.php';
    include 'admin-page-styles.php';
}

function wwt_view_consignment_stock_management($param) {
    include 'consignment-stock-management.php';
    include 'admin-page-styles.php';
}
