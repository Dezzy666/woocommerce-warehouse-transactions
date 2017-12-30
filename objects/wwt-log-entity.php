<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Object which represents one log entity.
 *
 * @class      WWT_LogEntity
 * @author     Jan Herzan
 */

class WWT_LogEntity {
    public $id;

    public $userId;
    public $productId;
    public $difference;
    public $note;
    public $orderId;

    public function __construct($userId, $productId, $difference, $note = '', $orderId = NULL) {
        $this->id = -1;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->difference = $difference;
        $this->note = $note;
        $this->orderId = $orderId;
    }

    public function save() {
        $this->insert();
    }

    private function insert() {
        global $wpdb;
        $wpdb->show_errors();
        $wpdb->insert(
            $wpdb->prefix . LOG_TABLE,
            array(
                'userId' => $this->userId,
                'productId' => $this->productId,
                'difference' => $this->difference,
                'notes' => $this->note,
                'orderId' => $this->orderId
            )
        );
    }

    public static function get_last($number = 20) {
        global $wpdb;
        $tableName = $wpdb->prefix . LOG_TABLE;
        $wpdb->show_errors();

        $result = $wpdb->get_results( "SELECT * FROM $tableName ORDER BY id DESC LIMIT $number", OBJECT );
        return $result;
    }

    public static function get_page($page = 0, $pageSize = 20) {
        global $wpdb;
        $tableName = $wpdb->prefix . LOG_TABLE;
        $offset = $page * $pageSize;

        $result = $wpdb->get_results( "SELECT * FROM $tableName ORDER BY id DESC LIMIT $pageSize OFFSET $offset", OBJECT );
        return $result;
    }

    public static function get_log_for_month($startMonth, $startYear, $endMonth, $endYear) {
        global $wpdb;
        $tableName = $wpdb->prefix . LOG_TABLE;

        $sql = "SELECT * FROM $tableName WHERE insertedAt >= '$startYear-$startMonth-01 00:00:00' AND insertedAt < '$endYear-$endMonth-01 00:00:00'";

        $result = $wpdb->get_results( $sql, OBJECT );
        return $result;
    }
}
