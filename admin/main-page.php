<?php

include_once(__DIR__ . '/../objects/wwt-log-entity.php');

$args     = array( 'post_type'   => 'product',
                   'numberposts' => -1,
                   'orderby'     => 'title',
                   'order'       => 'ASC');
$products = get_posts( $args );

if(isset($_POST['product-quantity']) && isset($_POST['product-id'])) {
    $userId = get_current_user_id();

    $product = wc_get_product($_POST['product-id']);

    if ($product && is_numeric($_POST['product-quantity'])) {
        wwt_update_product_stock($product, $_POST['product-quantity']);
        $newLog = new WWT_LogEntity($userId, $_POST['product-id'], $_POST['product-quantity'], $_POST['note']);
        $newLog->save();
    }
}

function wwt_update_product_stock($product, $quantity) {
    if (wwt_get_woocommerce_version() >= 3) {
        wc_update_product_stock($product, $quantity, 'increase');
    } else {
        wc_update_product_stock($product, $quantity + wc_get_product_stock($product));
    }
}

function wwt_get_user_name($userId) {
    if ($userId == NULL) return __('Order change', 'woocommerce-warehouse-transactions');

    $user = get_user_by('id', $userId);
    return $user->first_name . ' ' . $user->last_name;
}

function wwt_get_product_name($productId) {
    $product = wc_get_product($productId);
    return $product->get_title();
}

?>
<h1><?php _e('Warehouse movement log', 'woocommerce-warehouse-transactions'); ?></h1>
<div class="insertion">
    <form method="post">
        <h2><?php _e('Insert change', 'woocommerce-warehouse-transactions'); ?></h2>
        <select class="product-select" id="product-id" name="product-id">
            <?php
                foreach ($products as $product) {
                    $wcProduct = wc_get_product($product->ID);
                    if ($wcProduct->get_manage_stock()) {
                        echo '<option value="', $product->ID,'"">', apply_filters('wwt_main_page_dropdown_option', $product->ID . ' ' . $product->post_title, $wcProduct),'</option>';
                    }
                }
            ?>
        </select>
        <label for="product-quantity"><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></label><input type="number" id="product-quantity" name="product-quantity">
        <label for="product-quantity"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></label><input type="text" id="note" name="note">
        <div>
            <?php _e('+ value means added, - value means taken', 'woocommerce-warehouse-transactions'); ?>
        </div>
        <?php submit_button(__('Insert', 'woocommerce-warehouse-transactions')); ?>
    </form>
</div>

<div class="insertion">
    <h2><?php _e('Last changes', 'woocommerce-warehouse-transactions'); ?> </h2>
    <table class="recent">
        <tr>
            <th class="product-name"><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="user-name"><?php _e('User name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="difference"><?php _e('Difference', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>
        <?php
            $logNodes = WWT_LogEntity::get_last();
            foreach ($logNodes as $logNode) {
                echo '<tr><td class="product-name">', apply_filters('wwt_main_page_product_name_column', wwt_get_product_name($logNode->productId), wc_get_product($logNode->productId)),
                    '</td><td class="user-name">', wwt_get_user_name($logNode->userId),
                    '</td><td class="difference">', $logNode->difference,
                    '</td><td class="note">', $logNode->notes,
                    '</td></tr>';
            }
        ?>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery(".product-select").select2();
    });
</script>

<style>
    .insertion {
        width: 90%;
        background-color: white;
        padding: 5px 20px 20px 20px;
        border: 3px solid black;
        border-radius: 10px;
        margin-top: 10px;
        margin-bottom: 5px;
    }

    .product-select {
        width: 350px;
    }

    .recent .product-name {
        width: 250px;
        text-align: left;
    }


    .recent .user-name {
        width: 150px;
        text-align: left;
    }

    .recent .difference {
        text-align: left;
    }

    .recent .note {
        width: 450px;
        text-align: left;
    }

    .recent {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .recent tr:nth-child(2n) {
        background-color: lightgray;
    }

    span.select2-container {
        display: inline-block !important;
    }
</style>
