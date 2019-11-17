<?php

function add_warehous_record_over_rest() {
    return "OK";
}

function get_products_over_rest() {
    $isCeskeSluzbyUp = is_plugin_active('ceske-sluzby/ceske-sluzby.php');
    $products = get_product_list();

    $returnData = array();

    foreach ($products as $product) {
        $wcProduct = wc_get_product($product->ID);
        if ($wcProduct->get_manage_stock()) {
            if ($isCeskeSluzbyUp) {
                //// USE EAN
                $searchValue = get_post_meta($product->ID, 'ceske_sluzby_hodnota_ean', true);
            } else {
                //// USE SKU
                $searchValue = $wcProduct->get_sku();
            }

            $productData = array(
                'Sign' => apply_filters('wwt_main_page_dropdown_option', $product->ID . ' ' . $product->post_title, $wcProduct),
                'Id' => $product->ID,
                'SearchValue' => $searchValue
            );

            array_push($returnData, $productData);
        }
    }

    return $returnData;
}

add_action('rest_api_init', function () {
    register_rest_route( 'warehouse-transactions/v1', '/add-record', array(
                'methods' => 'POST',
                'callback' => 'add_warehous_record_over_rest',
                'permission_callback' => function () {
                    return current_user_can( 'wwt_rest_add_records' );
                }
            )
        );

    register_rest_route( 'warehouse-transactions/v1', '/get-products', array(
            'methods' => 'GET',
            'callback' => 'get_products_over_rest',
            'permission_callback' => function () {
                return current_user_can( 'wwt_rest_add_records' );
            }
        )
    );
    }
);
