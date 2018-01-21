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
    public $note;

    public function __construct($productId, $materialId, $volume, $note) {
        $this->id = -1;
        $this->productId = $productId;
        $this->materialId = $materialId;
        $this->volume = $volume;
        $this->note = $note;
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
                'notes' => $this->note
            )
        );
    }
}
