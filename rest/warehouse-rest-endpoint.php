<?php
include_once(__DIR__ . '/../admin/ap-warehouse-toolkit.php');


function add_warehous_record_over_rest($data) {
    $product = wc_get_product($data["productId"]);

    wwt_save_stock_change($product, $data['productId'], $data['count'], $data['note']);

    return "RecordCreated: [ProductId:" . $data["productId"] . ",Count:" . $data["count"] . ",Note:" . $data["note"] . "]";
}

function get_products_over_rest() {
    $isCeskeSluzbyUp = is_plugin_active('ceske-sluzby/ceske-sluzby.php');
    $products = get_product_list();

    $returnData = array();

    foreach ($products as $product) {
        $wcProduct = wc_get_product($product->ID);
        if ($wcProduct->get_manage_stock()) {
            $ean = "";

            if ($isCeskeSluzbyUp) {
                //// USE EAN
                $ean = get_post_meta($product->ID, 'ceske_sluzby_hodnota_ean', true);
            }

            //// USE SKU
            $sku = $wcProduct->get_sku();

            $productData = array(
                'name' => apply_filters('wwt_main_page_dropdown_option', $product->ID . ' ' . $product->post_title, $wcProduct),
                'productId' => $product->ID,
                'sku' => $sku,
                'ean' => $ean,
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
                'args' => array(
                    'productId' => array(
                        'validate_callback' => function($param, $request, $key) {
                            if (!is_numeric($param)) return false;

                            $product = wc_get_product($param);
                            return $product != null;
                        },
                        'required' => true
                    ),
                    'count' => array(
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric($param);
                        },
                        'required' => true
                    ),
                    'note' => array(
                        'required' => true
                    ),
                  ),
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
