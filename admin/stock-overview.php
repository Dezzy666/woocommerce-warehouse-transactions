<?php
$removedOrders = get_orders_ids_in_state(array("on-hold", "processing"));
?>
<h1><?php _e('Stock overview', 'woocommerce-warehouse-transactions'); ?></h1>
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
