<?php

function wwt_stock_whitepanel_content($post) {
    $postId = $post->ID;
    $order = wc_get_order($postId);

    ?>
    <table>
        <tr>
            <th><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></th>
            <th></th>
        </tr>
        <?php

        foreach ($order->get_items() as $item_id => $item) {
            $productId = $item->get_product_id();
            $name = $item->get_name();
            $quantity = $item->get_quantity();

            ?>
            <tr>
                <td><?php echo $name; ?></td>
                <td><?php echo $quantity; ?></td>
                <td><input data-order-id="<?php echo $postId; ?>"
                           data-product-id="<?php echo $productId; ?>"
                           data-quantity="<?php echo $quantity; ?>"
                           class="stock-in-and-out"
                           type="button"
                           value="<?php _e('Stock In and Out', 'woocommerce-warehouse-transactions'); ?>"></td>
            </tr>
            <?php
        }

        ?>
    </table>
    <script type="text/javascript">
        jQuery('.stock-in-and-out').click(function () {
            var buttonReference = jQuery(this);
            buttonReference.attr("disabled", true);
            
            var orderId = buttonReference.data("order-id");
            var productId = buttonReference.data("product-id");
            var quantity = buttonReference.data("quantity");

            console.log(orderId);
            console.log(productId);
            console.log(quantity);

            var data = {
                'action': 'wwt_perform_stock_up_and_down',
                'quantity': quantity,
                'productId': productId,
            };
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                buttonReference.css("background-color", '#32a852');
            });
        });
    </script>
     <?php
}

function wwt_create_stock_records_whitepanel() {
    add_meta_box(
        'wwt-stock-whitepanel',
        __( 'Warehouse operations', 'woocommerce-warehouse-transactions' ),
        'wwt_stock_whitepanel_content',
        'shop_order',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes_shop_order', 'wwt_create_stock_records_whitepanel');

function wwt_perform_stock_up_and_down() {
    $productId = intval($_POST["productId"]);
    $quantity = intval($_POST["quantity"]);
    $orderId = intval($_POST["orderId"]);

    $order = wc_get_order($orderId);
    $product = wc_get_product($productId);

    $userId = get_current_user_id();
    $note = __("Flip flop operation", 'woocommerce-warehouse-transactions');
    $orderNote = sprintf(__("Flip flop operation with %s. Quantity %s", 'woocommerce-warehouse-transactions'), $product->get_name(), $quantity);

    $positiveLog = new WWT_LogEntity($userId, $productId, $quantity, $note);
    $positiveLog->save();

    $negativeLog = new WWT_LogEntity($userId, $productId, -$quantity, $note);
    $negativeLog->save();

    $order->add_order_note($orderNote);

    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wwt_perform_stock_up_and_down', 'wwt_perform_stock_up_and_down' );
