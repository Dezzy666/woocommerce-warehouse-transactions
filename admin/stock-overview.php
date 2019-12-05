<?php

$calculatedData = wwt_calculate_current_stock();

$productData = $calculatedData["productData"];
$removedOrders = $calculatedData["removedOrders"];
$ignoredOrders = $calculatedData["ignoredOrders"];

?>
<h1><?php _e('Stock overview', 'woocommerce-warehouse-transactions'); ?></h1>

<table class="insertion-small">
    <tr>
        <th colspan="3"><?php _e('Current physical stock state', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>
    <tr>
        <th><?php _e('Product Id', 'woocommerce-warehouse-transactions'); ?></th>
        <th><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
        <th><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>

<?php
    foreach ($productData as $productId => $productSummary) {
        echo '<tr><td>', $productId, '</td><td>', $productSummary->name, '</td><td>', $productSummary->qty, '</td></tr>';
    }
?>

</table>

<table class="insertion-small">
    <tr>
        <th colspan="3"><?php _e('Current virtual stock state', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>
    <tr>
        <th><?php _e('Product Id', 'woocommerce-warehouse-transactions'); ?></th>
        <th><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
        <th><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>

<?php
    foreach ($productData as $productId => $productSummary) {
        echo '<tr><td>', $productId, '</td><td>', $productSummary->name, '</td><td>', $productSummary->initialQty, '</td></tr>';
    }
?>

</table>

<table class="insertion-small">
    <tr>
        <th colspan="2"><?php _e('Orders which were not sent', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>
    <tr>
        <th><?php _e('Order Id', 'woocommerce-warehouse-transactions'); ?></th>
        <th><?php _e('Order state', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>

<?php
    foreach ($removedOrders as $orderId => $orderStatus) {
        echo '<tr><td>', $orderId, '</td><td>', $orderStatus, '</td></tr>';
    }
?>

</table>

<table class="insertion-small">
    <tr>
        <th colspan="2">
            <?php _e('Orders which are not sent but ignored', 'woocommerce-warehouse-transactions'); ?><br>
            <?php _e('These orders are shipped from consignment stock', 'woocommerce-warehouse-transactions'); ?>
        </th>
    </tr>
    <tr>
        <th><?php _e('Order Id', 'woocommerce-warehouse-transactions'); ?></th>
        <th><?php _e('Order state', 'woocommerce-warehouse-transactions'); ?></th>
    </tr>

<?php
    foreach ($ignoredOrders as $orderId => $orderStatus) {
        echo '<tr><td>', $orderId, '</td><td>', $orderStatus, '</td></tr>';
    }
?>

</table>
