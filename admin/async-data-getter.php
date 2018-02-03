<?php

function wwt_get_data_page() {
    $page = $_POST['page'];

    $wrapper = array();
    $wrapper['page'] = $page;

    $data = wwt_log_transformer(WWT_LogEntity::get_page($page));
    $wrapper['data'] = $data;

    echo json_encode($wrapper);

    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wwt_get_data_page', 'wwt_get_data_page' );


function wwt_log_transformer($logNodes) {
    $transformedData = array();
    foreach ($logNodes as $logNode) {
        $node = array();
        $node["product-name"] = apply_filters('wwt_main_page_product_name_column', wwt_get_product_name($logNode->productId), wc_get_product($logNode->productId));
        $node["user-name"] = wwt_get_user_name($logNode->userId);
        $node["difference"] = $logNode->difference;
        $node["note"] = $logNode->notes;
        $node["inserted-at"] = $logNode->insertedAt;
        array_push($transformedData, $node);
    }

    return $transformedData;
}

function wwt_material_log_transformer($materialLogNodes) {
    $transformedData = array();
    foreach ($materialLogNodes as $materialLogNode) {
        $node = array();
        $node["material-name"] = $materialLogNode->name;

        if ($materialLogNode->productId == NULL) {
            $node["product-name"] = __('Material change added manually', 'woocommerce-warehouse-transactions');
        } else {
            $node["product-name"] = apply_filters('wwt_main_page_product_name_column', wwt_get_product_name($materialLogNode->productId), wc_get_product($materialLogNode->productId));
        }

        $node["user-name"] = wwt_get_user_name($materialLogNode->userId);
        $node["difference"] = $materialLogNode->difference;
        $node["note"] = $materialLogNode->notes;
        $node["inserted-at"] = $materialLogNode->insertedAt;
        array_push($transformedData, $node);
    }

    return $transformedData;
}
