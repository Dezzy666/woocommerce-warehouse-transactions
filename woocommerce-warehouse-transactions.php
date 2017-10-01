<?php
/**
 * Plugin Name: Woocommerce Warehouse Transactions
 * Description: Extension for woocommerce which keeps the track of warehouse changes.
 * Version: 0.0.1
 * Author: Jan Herzan
 * Author URI: http://jan.herzan.com
 * Requires at least: 4.4
 * WC requires at least: 3.0
 *
 * Text Domain: woocommerce-warehouse-transactions
 * Domain Path: /languages/
 */

include_once(__DIR__ . '/objects/wwt-log-entity.php');

$wwt_database_version = '1.0';

define('LOG_TABLE', 'woocommerce_warehouse_transactions_log_table');

function woocommerce_warehouse_transactions_install () {
    global $wpdb;

    $logTable = $wpdb->prefix . LOG_TABLE;

    $charset_collate = $wpdb->get_charset_collate();

    $sqlLogTable = "CREATE TABLE $logTable (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        userId mediumint(9) NULL,
        productId mediumint(9) NOT NULL,
        difference int NOT NULL,
        orderId mediumint(9) NULL,
        notes text DEFAULT '' NOT NULL,
        insertedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    )
    ENGINE=InnoDB
    $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sqlLogTable);

    add_option('wwt_database_version', $wwt_database_version);
}
register_activation_hook(__FILE__, 'woocommerce_warehouse_transactions_install');

function wwt_get_woocommerce_version() {
    $returnValue = intval(substr(WOOCOMMERCE_VERSION, 0, 1));
    return $returnValue;
}

/******************************************************************************/
/*              EXTERNAL LIBS                                                 */
/******************************************************************************/

function wwt_admin_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('jquery-ui', plugin_dir_url( __FILE__ ) . '/libs/jquery-ui/jquery-ui.min.js', array('jquery'), '1.11');
    wp_enqueue_script( 'select-2', plugin_dir_url( __FILE__ ) . '/libs/select2/select2.min.js', array('jquery', 'jquery-ui'), '4.0.3', true );

    wp_enqueue_style( 'jquery-ui-style', plugin_dir_url( __FILE__ ) . '/libs/jquery-ui/jquery-ui.min.css');
    wp_enqueue_style( 'select-2-style', plugin_dir_url( __FILE__ ) . '/libs/select2/select2.min.css');
}
add_action( 'admin_enqueue_scripts', 'wwt_admin_scripts' );


function wwt_localizationsample_init() {
    load_plugin_textdomain('woocommerce-warehouse-transactions', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}
add_action('init', 'wwt_localizationsample_init');

/******************************************************************************/
/*              RIGHTS                                                        */
/******************************************************************************/

function wwt_add_capability_to_admin() {
    $role_object = wp_roles()->get_role('administrator');
    $role_object->add_cap('wwt_rights');
}
add_action('admin_init', 'wwt_add_capability_to_admin');

function wwt_remove_stock_tab($tabs) {
    if (!current_user_can('manage_options')) {
        unset($tabs['inventory']);
    }
    return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'wwt_remove_stock_tab');

/******************************************************************************/
/*              ORDER MANAGEMENT                                              */
/******************************************************************************/

function wwt_create_log_reduce($orderOrId) {
    $order = new WC_Order($orderOrId);
    $orderId = $order->id;

    $itemList = $order->get_items();

    foreach ($itemList as $item) {
        $newLog = new WWT_LogEntity(NULL, $item['product_id'], -$item['qty'], sprintf(__('Amout reduced because of order %d', 'woocommerce-warehouse-transactions'), $orderId), $orderId);
        $newLog->save();
    }
}
add_action('woocommerce_reduce_order_stock', 'wwt_create_log_reduce');

function wwt_create_log_restore($orderOrId) {
    $order = new WC_Order($orderOrId);
    $orderId = $order->id;

    $itemList = $order->get_items();

    foreach ($itemList as $item) {
        $newLog = new WWT_LogEntity(NULL, $item['product_id'], $item['qty'], sprintf(__('Amout restored because of order %d was canceled', 'woocommerce-warehouse-transactions'), $orderId), $orderId);
        $newLog->save();
    }
}
add_action('woocommerce_restore_order_stock', 'wwt_create_log_restore');

function wwt_create_log_restock($productId, $oldStock, $newStock, $order, $product) {
    $newLog = new WWT_LogEntity(NULL, $productId, $oldStock - $newStock, sprintf(__('Amout changed because of order %d was changed', 'woocommerce-warehouse-transactions'), $order->id), $order->id);
    $newLog->save();
}
add_action('woocommerce_restock_refunded_item', 'wwt_create_log_restock', 10, 5);

/******************************************************************************/
/*              INCLUDES                                                      */
/******************************************************************************/

include 'admin/admin-menu-setup.php';
