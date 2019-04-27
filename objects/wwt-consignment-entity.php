<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Object which represents one log entity for consignment storage.
 *
 * @class      WWT_ConsignmentEntity
 * @author     Jan Herzan
 */

class WWT_ConsignmentEntity {
    public $id;

    public $name;
    public $description;
    public $paymentMethods;
    public $insertedAt;

    public function __construct($name, $description, $paymentMethods) {
        $this->id = -1;

        $this->name = $name;
        $this->description = $description;
        $this->paymentMethods = $paymentMethods;
    }

    public function save() {
        $this->insert();
    }

    private function insert() {
        global $wpdb;

        $wpdb->show_errors();
        $wpdb->insert(
            $wpdb->prefix . CONSINMENT_LIST_TABLE,
            array(
                'name' => $this->name,
                'description' => $this->description,
                'paymentMethods' => $this->paymentMethods
            )
        );
    }

    public static function get_all() {
        global $wpdb;
        $tableName = $wpdb->prefix . CONSINMENT_LIST_TABLE;
        $wpdb->show_errors();

        $result = $wpdb->get_results( "SELECT * FROM $tableName ORDER BY id", OBJECT );
        return $result;
    }

    public static function set_payment_methods($consignmentId, $paymentMethods) {
        global $wpdb;
        $tableName = $wpdb->prefix . CONSINMENT_LIST_TABLE;

        $wpdb->update(
            $tableName, 
            array( 
                'paymentMethods' => $paymentMethods    // string
            ),
            array( 'ID' => $consignmentId )
        );
    }

    public static function update_product($consignmentId, $productId, $diff) {
        global $wpdb;
        $productTable = $wpdb->prefix . CONSINMENT_PRODUCT_TABLE;

        $wpdb->query("START TRANSACTION");
        $wpdb->query("
                    INSERT INTO $productTable
                        (`consignmentListId`, `productId`)
                        VALUES
                        ($consignmentId, $productId)
                        ON DUPLICATE KEY UPDATE productId=productId;");
        $wpdb->query("UPDATE $productTable
                        SET quantity = quantity + $diff
                        WHERE productId = $productId AND consignmentListId = $consignmentId
                    ");
        $wpdb->query("COMMIT"); // if you come here then well done
    }
}
