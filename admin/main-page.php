<?php

include_once('/../objects/wwt-log-entity.php');

$args     = array( 'post_type' => 'product' );
$products = get_posts( $args );

if(isset($_POST['product-quantity']) && isset($_POST['product-id'])) {
    $userId = get_current_user_id();
    $newLog = new WWT_LogEntity($userId, $_POST['product-id'], $_POST['product-quantity'], $_POST['note']);
    $newLog->save();
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
    }

    .product-select {
        width: 350px;
    }
</style>
