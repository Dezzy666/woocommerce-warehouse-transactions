<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Object which represents one material log entity which contains changes of material
 * quantity on stock.
 *
 * @class      WWT_MaterialLogEntity
 * @author     Jan Herzan
 */

class WWT_MaterialLogEntity {
    public $id;

    public $userId;
    public $productId;
    public $difference;
    public $materialId;
    public $notes;
    public $insertedAt;

    public function __construct($userId, $productId, $difference, $materialId, $notes = '') {
        $this->id = -1;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->difference = $difference;
        $this->materialId = $materialId;
        $this->notes = $notes;
    }

    public function save() {
        $this->insert();
    }

    private function insert() {
        global $wpdb;
        $wpdb->show_errors();
        $wpdb->insert(
            $wpdb->prefix . MATERIAL_LOG_TABLE,
            array(
                'userId' => $this->userId,
                'productId' => $this->productId,
                'difference' => $this->difference,
                'materialId' => $this->materialId,
                'notes' => $this->notes
            )
        );
    }

    public static function get_last($number = 20) {
        global $wpdb;
        $tableName = $wpdb->prefix . MATERIAL_LOG_TABLE;
        $materialTable = $wpdb->prefix . MATERIAL_TABLE;
        $wpdb->show_errors();

        $result = $wpdb->get_results("SELECT log.*, mat.name
            FROM $tableName log
            JOIN $materialTable mat ON log.materialId = mat.Id
            ORDER BY log.id DESC LIMIT $number" , OBJECT );
        return $result;
    }

    public static function get_page($page = 0, $pageSize = 20) {
        global $wpdb;
        $tableName = $wpdb->prefix . MATERIAL_LOG_TABLE;
        $offset = $page * $pageSize;

        $result = $wpdb->get_results( "SELECT * FROM $tableName ORDER BY id DESC LIMIT $pageSize OFFSET $offset", OBJECT );
        return $result;
    }
}
