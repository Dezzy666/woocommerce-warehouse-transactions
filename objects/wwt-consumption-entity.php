<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Object which represents connection between product and material with
 * consumption parameter.
 *
 * @class      WWT_LogEntity
 * @author     Jan Herzan
 */

class WWT_ConsumptionEntity {
    public $id;

    public $productId;
    public $materialId;
    public $volume;
    public $notes;

    public function __construct($productId, $materialId, $volume, $notes) {
        $this->id = -1;
        $this->productId = $productId;
        $this->materialId = $materialId;
        $this->volume = $volume;
        $this->note = $notes;
    }

    public function save() {
        $this->insert();
    }

    public function insert() {
        global $wpdb;
        $wpdb->show_errors();
        $wpdb->insert(
            $wpdb->prefix . CONSUMPTION_TABLE,
            array(
                'productId' => $this->productId,
                'materialId' => $this->materialId,
                'volume' => $this->volume,
                'notes' => $this->notes
            )
        );
    }

    public static function get_consumptions() {
        global $wpdb;
        $tableName = $wpdb->prefix . CONSUMPTION_TABLE;
        $materialTableName = $wpdb->prefix . MATERIAL_TABLE;
        $wpdb->show_errors();

        $result = $wpdb->get_results("
            SELECT consumption.*, material.name as materialName
            FROM $tableName consumption
            JOIN $materialTableName material ON consumption.materialId = material.id
            ORDER BY productId");
        return $result;
    }
}
