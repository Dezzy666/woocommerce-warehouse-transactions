<?php
/**
 * Plugin Name: Woocommerce Warehouse Transactions
 * Description: Extension for woocommerce which keeps the track of warehouse changes.
 * Version: 0.0.1
 * Author: Jan Herzan
 * Author URI: http://jan.herzan.com
 * Requires at least: 4.4
 *
 * Text Domain: woocommerce-warehouse-transactions
 * Domain Path: /languages/
 */

 $wwt_database_version = '1.0';

 define('LOG_TABLE', 'woocommerce_warehouse_transactions_log_table');

function woocommerce_warehouse_transactions_install () {
    global $wpdb;

    $logTable = $wpdb->prefix . LOG_TABLE;

    $charset_collate = $wpdb->get_charset_collate();

    $sqlLogTable = "CREATE TABLE $logTable (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        userId mediumint(9) NOT NULL,
        productId mediumint(9) NOT NULL,
        difference int NOT NULL,
        orderId mediumint(9) NULL,
        notes text DEFAULT '' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sqlLogTable);

    add_option('wwt_database_version', $wwt_database_version);
}
register_activation_hook(__FILE__, 'woocommerce_warehouse_transactions_install');
