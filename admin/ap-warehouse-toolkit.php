<?php

include_once(__DIR__ . '/../objects/wwt-log-entity.php');
include_once(__DIR__ . '/../objects/wwt-material-log-entity.php');
include_once(__DIR__ . '/../objects/wwt-consumption-entity.php');

function wwt_save_stock_change($product, $productId, $quantity, $note) {
    wwt_update_product_stock($product, $quantity);
    $newLog = new WWT_LogEntity($userId, $productId, $quantity, $note);
    $newLog->save();
}

function wwt_create_consumption_log($productId, $quantity, $userId, $note) {
    $consumptions = WWT_ConsumptionEntity::get_consumptions_for_product($productId);

    foreach ($consumptions as $consumption) {
        $consumptionLog = new WWT_MaterialLogEntity(
            $userId,
            $productId,
            $consumption->volume * $quantity,
            $consumption->materialId,
            $note
        );
        $consumptionLog->save();

        WWT_MaterialEntity::increment_material_quantity($consumption->materialId, -1 * $consumption->volume * $quantity);
    }
}

function wwt_update_product_stock($product, $quantity) {
    if (wwt_get_woocommerce_version() >= 3) {
        wc_update_product_stock($product, $quantity, 'increase');
    } else {
        wc_update_product_stock($product, $quantity + wc_get_product_stock($product));
    }
}
