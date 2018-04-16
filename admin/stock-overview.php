<?php
$removedOrders = get_orders_ids_in_state(array("on-hold", "processing"));

$products = get_product_list();
$productData = array();

foreach ($products as $product) {
    $wcProduct = wc_get_product($product->ID);
    if ($wcProduct->get_manage_stock()) {
        $productData[$product->ID] = (object)
            [
                'name' => apply_filters('wwt_main_page_product_name_column', $wcProduct->get_title(), $wcProduct),
                'qty' => $wcProduct->get_stock_quantity()
            ];
    }
}


foreach ($removedOrders as $orderId => $orderStatus) {
    $order = wc_get_order( $orderId );
    foreach ($order->get_items() as $itemId => $itemData) {
        $orderProduct = $itemData->get_product();
        $orderProductId = $orderProduct->get_id();
        $itemQuantity = $itemData->get_quantity();

        if ($orderProduct->get_manage_stock() && array_search($orderProductId, $productData) !== -1) {
            $productData[$orderProductId]->qty += $itemQuantity;
        }
    }
}

?>
<h1><?php _e('Stock overview', 'woocommerce-warehouse-transactions'); ?></h1>

<table class="insertion-small">
    <tr>
        <th colspan="3"><?php _e('Current stock quantity', 'woocommerce-warehouse-transactions'); ?></th>
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
