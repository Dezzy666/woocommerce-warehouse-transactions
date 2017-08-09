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
    )
    ENGINE=InnoDB
    $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sqlLogTable);

    add_option('wwt_database_version', $wwt_database_version);
}
register_activation_hook(__FILE__, 'woocommerce_warehouse_transactions_install');

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

