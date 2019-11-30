<?php
/**
 * Plugin Name: Woocommerce Warehouse Transactions
 * Description: Extension for woocommerce which keeps the track of warehouse changes.
 * Version: 2.5.0
 * Author: Jan Herzan
 * Author URI: http://jan.herzan.com
 * Requires at least: 4.4
 * WC requires at least: 3.0
 *
 * Text Domain: woocommerce-warehouse-transactions
 * Domain Path: /languages/
 */

define('WWT_PLUGIN_PATH', __FILE__);

include_once(__DIR__ . '/objects/wwt-log-entity.php');
include_once(__DIR__ . '/objects/wwt-material-entity.php');
include_once(__DIR__ . '/objects/wwt-consumption-entity.php');
include_once(__DIR__ . '/objects/wwt-consignment-entity.php');
include_once(__DIR__ . '/objects/wwt-consignment-log-entity.php');


define('LOG_TABLE', 'woocommerce_warehouse_transactions_log_table');
define('MATERIAL_LOG_TABLE', 'woocommerce_warehouse_transactions_material_log_table');
define('MATERIAL_TABLE', 'woocommerce_warehouse_transactions_material_table');
define('CONSUMPTION_TABLE', 'woocommerce_warehouse_transactions_consumption_table');
define('CONSINMENT_LIST_TABLE', 'woocommerce_warehouse_transactions_consignment_list');
define('CONSINMENT_LOG_TABLE', 'woocommerce_warehouse_transactions_consignment_log');
define('CONSINMENT_PRODUCT_TABLE', 'woocommerce_warehouse_transactions_consignment_products');

define('TABLE_VERSION', 'wwt_database_version');

define('WWT_STOCK_REDUCED_FLAG', '_reduced_stock_logged');

function woocommerce_warehouse_transactions_install () {
    global $wpdb;
    $wwt_database_version = '2.3';

    $actualVersion = get_option(TABLE_VERSION, '');

    $logTable = $wpdb->prefix . LOG_TABLE;
    $materialTable = $wpdb->prefix . MATERIAL_TABLE;
    $consumptionTable = $wpdb->prefix . CONSUMPTION_TABLE;
    $materialLogTable = $wpdb->prefix . MATERIAL_LOG_TABLE;

    $consignmentListTable = $wpdb->prefix . CONSINMENT_LIST_TABLE;
    $consignmentLogTable = $wpdb->prefix . CONSINMENT_LOG_TABLE;
    $consignmentProductTable = $wpdb->prefix . CONSINMENT_PRODUCT_TABLE;

    if ($actualVersion !== $wwt_database_version) {

            $charset_collate = $wpdb->get_charset_collate();

            $sqlLogTable = "CREATE TABLE $logTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userId mediumint(9) NULL,
                productId mediumint(9) NOT NULL,
                difference int NOT NULL,
                orderId mediumint(9) NULL,
                notes text DEFAULT '' NOT NULL,
                insertedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                newValue mediumint(9) NULL,
                PRIMARY KEY  (id)
            )
            ENGINE=InnoDB
            $charset_collate;";

            $sqlConsinmentListTable = "CREATE TABLE $consignmentListTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                description text DEFAULT '' NOT NULL,
                paymentMethods text DEFAULT '' NOT NULL,
                insertedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            )
            ENGINE=InnoDB
            $charset_collate;";

            $sqlConsinmentLogTable = "CREATE TABLE $consignmentLogTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                consignmentListId mediumint(9) NOT NULL,
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

            $sqlConsignmentProductTable = "CREATE TABLE $consignmentProductTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                consignmentListId mediumint(9) NOT NULL,
                productId mediumint(9) NOT NULL,
                quantity int NOT NULL DEFAULT 0,
                lastUpdate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY  consignment_list_unique_id (consignmentListId,productId)
            )
            ENGINE=InnoDB
            $charset_collate;";

            $sqlMaterialTable = "CREATE TABLE $materialTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                unit varchar(10) NOT NULL,
                volume DECIMAL(10,4) NOT NULL,
                notes text DEFAULT '' NOT NULL,
                insertedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            )
            ENGINE=InnoDB
            $charset_collate;";

            $sqlConsumptionTable = "CREATE TABLE $consumptionTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                productId mediumint(9) NOT NULL,
                materialId mediumint(9) NOT NULL,
                volume DECIMAL(10,4) NOT NULL,
                notes text DEFAULT '' NOT NULL,
                insertedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            )
            ENGINE=InnoDB
            $charset_collate;";

            $sqlMaterialLogTable = "CREATE TABLE $materialLogTable (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userId mediumint(9) NULL,
                productId mediumint(9) NULL,
                difference DECIMAL(10,4) NOT NULL,
                materialId mediumint(9) NOT NULL,
                notes text DEFAULT '' NOT NULL,
                insertedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            )
            ENGINE=InnoDB
            $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sqlLogTable);
            dbDelta($sqlConsinmentListTable);
            dbDelta($sqlConsinmentLogTable);
            dbDelta($sqlConsignmentProductTable);
            dbDelta($sqlMaterialTable);
            dbDelta($sqlConsumptionTable);
            dbDelta($sqlMaterialLogTable);

            add_option(TABLE_VERSION, $wwt_database_version);
    }
}
add_action('plugins_loaded', 'woocommerce_warehouse_transactions_install');

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
    $role_object->add_cap('wwt_recepies_rights');

    $role_object->add_cap('wwt_rest_add_records');
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
/*              JS & CSS DEPENDENCIES                                         */
/******************************************************************************/

function wwt_include_javascript_and_css() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui', plugin_dir_url('js/libs/jquery-ui-1.11.2.min.js', __FILE__), array('jquery'), '1.11.2');
    wp_enqueue_script('barcode-component', plugin_dir_url(__FILE__) . 'js/SimpleBarcodeApi.js', array('jquery', 'jquery-ui'));

    wp_enqueue_style('jquery-ui', plugins_url('js/libs/jquery-ui-1.11.2.min.css', __FILE__));
}
add_action( 'admin_enqueue_scripts', 'wwt_include_javascript_and_css' );

/******************************************************************************/
/*              CRON TASK INSERTION                                           */
/******************************************************************************/

function wwt_register_cron() {
    wp_clear_scheduled_hook('wwt_send_email_with_log');
    $firstDayNextMonth = date('Y-m-d 03:00:00', strtotime('first day of next month'));
    wp_schedule_event(strtotime($firstDayNextMonth), 'monthly', 'wwt_send_email_with_log');

    return $firstDayNextMonth;
}
register_activation_hook(__FILE__, 'wwt_register_cron');

function wwt_insert_cron() {
    $date = wwt_register_cron();
    echo sprintf(__('Cron for WWT successfully inserted. Next execution day is %s', 'woocommerce-warehouse-transactions'), $date);

    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wwt_insert_cron', 'wwt_insert_cron' );

/******************************************************************************/
/*              SETTINGS PROPERTIES                                           */
/******************************************************************************/

define('WWT_SETTING_GROUP', 'wwt_setting_group');
define('WWT_REPORT_EMAIL', 'wwt_report_email');

function wwt_override_register_setting($settingName) {
	register_setting( WWT_SETTING_GROUP, $settingName);
	add_filter('pre_update_option_'. $settingName, 'wwt_update_option', 10, 2);
}

function wwt_update_option($newValue, $oldValue) {
	if ($newValue == null) {
		return $oldValue;
	}
	return $newValue;
}

function wwt_register_settings() {
    wwt_override_register_setting(WWT_REPORT_EMAIL);
}
add_action('admin_init', 'wwt_register_settings');

/******************************************************************************/
/*              ORDER MANAGEMENT                                              */
/******************************************************************************/

function wwt_get_user_name($userId) {
    if ($userId == NULL) return __('Order change', 'woocommerce-warehouse-transactions');

    $user = get_user_by('id', $userId);
    return $user->first_name . ' ' . $user->last_name;
}

function wwt_get_product_name($productId) {
    $product = wc_get_product($productId);
    return $product->get_title();
}

function wwt_create_log_reduce_quantity($order) {
    $orderId = $order->id;
    $orderItems = $order->get_items();
    if (get_option('woocommerce_manage_stock') == 'yes' && sizeof($orderItems) > 0) {
        foreach ( $orderItems as $itemId => $orderItem ) {
            $itemStockReduced = $orderItem->get_meta( WWT_STOCK_REDUCED_FLAG, true );
            $product = $orderItem->get_product();
            if (!$itemStockReduced && $product && $product->exists() && $product->managing_stock()) {
                $productId = $product->get_id();
                $quantity = $orderItem->get_quantity();
                $orderItem->add_meta_data( WWT_STOCK_REDUCED_FLAG, $quantity, true );
                $orderItem->save();

                $newLog = new WWT_LogEntity(NULL, $productId, -$quantity, sprintf(__('Amout reduced because of change in order %d.', 'woocommerce-warehouse-transactions'), $orderId), $orderId);
                $newLog->save();
            }
        }
    }
}
add_action('woocommerce_reduce_order_stock', 'wwt_create_log_reduce_quantity');

function wwt_create_log_restore_quantity($order) {
    $orderId = $order->id;
    $orderItems = $order->get_items();
    if (get_option('woocommerce_manage_stock') == 'yes' && sizeof($orderItems) > 0) {
        foreach ( $orderItems as $itemId => $orderItem ) {
            $itemStockReduced = $orderItem->get_meta( WWT_STOCK_REDUCED_FLAG, true );
            $product = $orderItem->get_product();
            if ($itemStockReduced && $product && $product->exists() && $product->managing_stock()) {
                $productId = $product->get_id();
                $quantity = $orderItem->get_quantity();
                $orderItem->delete_meta_data( WWT_STOCK_REDUCED_FLAG );
                $orderItem->save();

                $newLog = new WWT_LogEntity(NULL, $productId, $quantity, sprintf(__('Amout restored because of change in order %d.', 'woocommerce-warehouse-transactions'), $orderId), $orderId);
                $newLog->save();
            }
        }
    }
}
add_action('woocommerce_restore_order_stock', 'wwt_create_log_restore_quantity');

function wwt_create_log_restock($productId, $oldStock, $newStock, $order, $product) {
    $newLog = new WWT_LogEntity(NULL, $productId, $oldStock - $newStock, sprintf(__('Amout changed because of order %d was refunded', 'woocommerce-warehouse-transactions'), $order->id), $order->id);
    $newLog->save();
}
add_action('woocommerce_restock_refunded_item', 'wwt_create_log_restock', 10, 5);

function add_wwt_hidden_order_itemmeta($metaArray) {
    array_push($metaArray, WWT_STOCK_REDUCED_FLAG);

    return $metaArray;
}
add_filter('woocommerce_hidden_order_itemmeta', 'add_wwt_hidden_order_itemmeta');

/******************************************************************************/
/*              CONSIGNMENT STOCK LOGIC                                       */
/******************************************************************************/

function wwt_perform_consignment_stocks_increase($status, $order) {
    $shippingMethod = get_shipping_method_with_id($order);
    $consignmentStocks = WWT_ConsignmentEntity::get_all();

    foreach ($consignmentStocks as $consignment) {
        $consignmentStockShippingMethods = explode(FIELDS_SEPARATOR, $consignment->paymentMethods);

        if (in_array($shippingMethod, $consignmentStockShippingMethods)) {
            $order->add_order_note(sprintf(__('Goods were taken from consignment stock [%s]. No main warehouse changes.', 'medinatur_v3'), $consignment->name));

            $products = $order->get_items();

            foreach ($products as $product) {
                $itemStockReduced = $product->get_meta( WWT_STOCK_REDUCED_FLAG, true );

                if (!$itemStockReduced) {
                    $quantity = $product->get_quantity();
                    $productId = $product->get_product_id();
                    WWT_ConsignmentEntity::update_product($consignment->id, $productId, -$quantity);
                    $logEntry = new WWT_ConsignmentLogEntity($consignment->id, NULL, $productId, -$quantity, sprintf(__('Amout reduced because of change in order %d.', 'woocommerce-warehouse-transactions'), $order->id), $order->id);
                    $logEntry->save();
                    $product->add_meta_data( WWT_STOCK_REDUCED_FLAG, $quantity, true );
                    $product->save();
                }
            }

            $status = false;
        }
    }

    return $status;
}
add_filter('woocommerce_can_reduce_order_stock', 'wwt_perform_consignment_stocks_increase', 10, 2);

function wwt_perform_consignment_stocks_decrease($status, $order) {
    $shippingMethod = get_shipping_method_with_id($order);
    $consignmentStocks = WWT_ConsignmentEntity::get_all();

    foreach ($consignmentStocks as $consignment) {
        $consignmentStockShippingMethods = explode(FIELDS_SEPARATOR, $consignment->paymentMethods);

        if (in_array($shippingMethod, $consignmentStockShippingMethods)) {
            $order->add_order_note(sprintf(__('Goods were taken from consignment stock [%s]. No main warehouse changes.', 'medinatur_v3'), $consignment->name));

            $products = $order->get_items();

            foreach ($products as $product) {
                $itemStockReduced = $product->get_meta( WWT_STOCK_REDUCED_FLAG, true );

                if ($itemStockReduced) {
                    $quantity = $product->get_quantity();
                    $productId = $product->get_product_id();
                    WWT_ConsignmentEntity::update_product($consignment->id, $productId, $quantity);
                    $logEntry = new WWT_ConsignmentLogEntity($consignment->id, NULL, $productId, $quantity, sprintf(__('Amout restored because of change in order %d.', 'woocommerce-warehouse-transactions'), $order->id), $order->id);
                    $logEntry->save();
                    $product->delete_meta_data( WWT_STOCK_REDUCED_FLAG );
                    $product->save();
                }
            }

            $status = false;
        }
    }

    return $status;
}
add_filter('woocommerce_can_restore_order_stock', 'wwt_perform_consignment_stocks_decrease', 10, 2);

/******************************************************************************/
/*              INCLUDES                                                      */
/******************************************************************************/

include 'admin/admin-menu-setup.php';
include 'admin/async-data-getter.php';
include 'cron/email-report.php';
include 'updater/updater.php';

include 'toolbox/admin-menu-tools.php';
include 'toolbox/aggregated-information.php';
include 'toolbox/properties-handling.php';
include 'toolbox/woocommerce-wordpress-lists.php';

include 'rest/warehouse-rest-endpoint.php';
