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
}
