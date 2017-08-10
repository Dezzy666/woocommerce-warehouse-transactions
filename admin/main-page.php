<?php

include_once('/../objects/wwt-log-entity.php');

$args     = array( 'post_type'   => 'product',
                   'numberposts' => -1);
$products = get_posts( $args );

if(isset($_POST['product-quantity']) && isset($_POST['product-id'])) {
    $userId = get_current_user_id();

    $product = wc_get_product($_POST['product-id']);

    if ($product && is_numeric($_POST['product-quantity'])) {
        wc_update_product_stock($product, $_POST['product-quantity'], 'increase');
        $newLog = new WWT_LogEntity($userId, $_POST['product-id'], $_POST['product-quantity'], $_POST['note']);
        $newLog->save();
    }
}

function wwt_get_user_name($userId) {
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
                    echo '<option value="', $product->ID,'"">', $product->post_title,'</option>';
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
            <th><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th><?php _e('User name', 'woocommerce-warehouse-transactions'); ?></th>
            <th><?php _e('Difference', 'woocommerce-warehouse-transactions'); ?></th>
            <th><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>
        <?php
            $logNodes = WWT_LogEntity::get_last();
            foreach ($logNodes as $logNode) {
                echo '<tr><td>', wwt_get_product_name($logNode->productId),
                    '</td><td>', wwt_get_user_name($logNode->userId),
                    '</td><td>', $logNode->difference,
                    '</td><td>', $logNode->notes,
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

    .recent th, .recent td {
        width: 150px;
        text-align: center;
    }
</style>
