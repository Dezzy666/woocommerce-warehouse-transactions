<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Object which represents one specific material which is used in production.
 *
 * @class      WWT_MaterialEntity
 * @author     Jan Herzan
 */

class WWT_MaterialEntity {
    public $id;

    public $name;
    public $unit;
    public $volume;
    public $notes;

    public function __construct($name, $unit, $volume, $notes = '') {
        $this->id = -1;
        $this->name = $name;
        $this->unit = $unit;
        $this->volume = $volume;
        $this->notes = $notes;
    }

    public function save() {
        $this->insert();
    }

    private function insert() {
        global $wpdb;
        $wpdb->show_errors();
        $wpdb->insert(
            $wpdb->prefix . MATERIAL_TABLE,
            array(
                'name' => $this->name,
                'unit' => $this->unit,
                'volume' => $this->volume,
                'notes' => $this->notes
            )
        );
    }

    public static function get_materials() {
        global $wpdb;
        $tableName = $wpdb->prefix . MATERIAL_TABLE;
        $wpdb->show_errors();

        $result = $wpdb->get_results("SELECT * FROM $tableName ORDER BY id");
        return $result;
    }

    public static function increment_material_quantity($materialId, $quantity) {
        global $wpdb;
        $tableName = $wpdb->prefix . MATERIAL_TABLE;
        $wpdb->show_errors();

        $wpdb->query("UPDATE $tableName
                      SET volume = volume + $quantity
                      WHERE id = $materialId");
    }
}
