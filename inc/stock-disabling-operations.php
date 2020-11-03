<?php

define('SKIP_STOCK_OPERATIONS', 'wwt_skip_stock_operations');

function wwt_stock_whitepanel_content($post) {
    $postId = $post->ID;
    $order = wc_get_order($postId);

    ?>
    <table>
        <tr>
            <th><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th><?php _e('Total quantity', 'woocommerce-warehouse-transactions'); ?></th>
            <th><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></th>
            <th></th>
            <th></th>
        </tr>
        <?php

        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $productId = $product->get_id();
            $name = $item->get_name();
            $quantity = $item->get_quantity();

            ?>
            <tr>
                <td><?php echo apply_filters('wwt_main_page_dropdown_option', $product->get_id() . ' ' . $product->get_name(), $product); ?></td>
                <td><input type="number" id="product-quantity-<?php echo $productId; ?>" value="<?php echo $quantity; ?>"
                           min="1" max="<?php echo $quantity; ?>"></td>
                <td><?php echo $quantity; ?></td>
                <td><input data-order-id="<?php echo $postId; ?>"
                           data-product-id="<?php echo $productId; ?>"
                           class="stock-in-and-out"
                           type="button"
                           id="stock-in-and-out-<?php echo $productId; ?>"
                           value="<?php _e('Stock In and Out', 'woocommerce-warehouse-transactions'); ?>"></td>
                <td><input data-order-id="<?php echo $postId; ?>"
                           data-product-id="<?php echo $productId; ?>"
                           class="stock-out-only"
                           id="stock-out-only-<?php echo $productId; ?>"
                           type="button"
                           value="<?php _e('Stock Out', 'woocommerce-warehouse-transactions'); ?>"></td>
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
            var quantity = jQuery("#product-quantity-" + productId).val();

            var otherButton = jQuery("#stock-out-only-" + productId);
            otherButton.attr("disabled", true);

            console.log(orderId);
            console.log(productId);
            console.log(quantity);

            var data = {
                'action': 'wwt_perform_stock_up_and_down',
                'quantity': quantity,
                'productId': productId,
                'orderId': orderId,
            };
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                buttonReference.css("background-color", '#32a852');
            });
        });

        jQuery('.stock-out-only').click(function () {
            var buttonReference = jQuery(this);
            buttonReference.attr("disabled", true);

            var orderId = buttonReference.data("order-id");
            var productId = buttonReference.data("product-id");
            var quantity = jQuery("#product-quantity-" + productId).val();

            var otherButton = jQuery("#stock-in-and-out-" + productId);
            otherButton.attr("disabled", true);

            console.log(orderId);
            console.log(productId);
            console.log(quantity);

            var data = {
                'action': 'wwt_perform_stock_down_only',
                'quantity': quantity,
                'productId': productId,
                'orderId': orderId,
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
    $completeProductName = apply_filters('wwt_main_page_dropdown_option', $product->get_id() . ' ' . $product->get_name(), $product);

    $userId = get_current_user_id();
    $note = sprintf(__("Flip flop operation on order %s", 'woocommerce-warehouse-transactions'), $orderId);
    $orderNote = sprintf(__("Flip flop operation with %s. Quantity %s", 'woocommerce-warehouse-transactions'), $completeProductName, $quantity);

    $positiveLog = new WWT_LogEntity($userId, $productId, $quantity, $note);
    $positiveLog->save();

    $negativeLog = new WWT_LogEntity($userId, $productId, -$quantity, $note);
    $negativeLog->save();

    $order->add_order_note($orderNote);

    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wwt_perform_stock_up_and_down', 'wwt_perform_stock_up_and_down' );

function wwt_perform_stock_down_only() {
    $productId = intval($_POST["productId"]);
    $quantity = intval($_POST["quantity"]);
    $orderId = intval($_POST["orderId"]);

    $order = wc_get_order($orderId);
    $product = wc_get_product($productId);
    $completeProductName = apply_filters('wwt_main_page_dropdown_option', $product->get_id() . ' ' . $product->get_name(), $product);
    $note = sprintf(__("Stock down operation on order %s", 'woocommerce-warehouse-transactions'), $orderId);
    $orderNote = sprintf(__("Stock down operation with %s. Quantity %s", 'woocommerce-warehouse-transactions'), $completeProductName, $quantity);

    wwt_save_stock_change($product, $productId, -$quantity, $note);
    $order->add_order_note($orderNote);

    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_wwt_perform_stock_down_only', 'wwt_perform_stock_down_only' );

function wwt_check_client_id($status, $order) {
    $userId = $order->get_user_id();

    if ($userId == 0) {
        return $status;
    } else {
        $skipStockManagementFlag = get_user_meta($userId, SKIP_STOCK_OPERATIONS, true);

        if ($skipStockManagementFlag == "1") {
            $order->add_order_note(__('User is set to skip stock changes. No main warehouse changes.', 'medinatur_v3'));
            return false;
        }
    }

    return $status;
}
add_filter('woocommerce_can_reduce_order_stock', 'wwt_check_client_id', 10, 2);
add_filter('woocommerce_can_restore_order_stock', 'wwt_check_client_id', 10, 2);

function wwt_extra_profile_fields($user) {
    ?>
    <div>
        <h3><?php _e('WWT', 'woocommerce-warehouse-transactions'); ?></h3>
        <?php $value = get_user_meta( $user->ID, SKIP_STOCK_OPERATIONS, true ) === "1" ? "checked" : "" ; ?>
        <input type="checkbox" name="<?php echo SKIP_STOCK_OPERATIONS ?>" id="<?php echo SKIP_STOCK_OPERATIONS ?>" value="1" <?php echo $value; ?>/>
        <label for="<?php echo SKIP_STOCK_OPERATIONS ?>"><?php _e('Skip stock operations for this user', 'woocommerce-warehouse-transactions'); ?></label>
    </div>
    <?php
}
add_action( 'show_user_profile', 'wwt_extra_profile_fields' );
add_action( 'edit_user_profile', 'wwt_extra_profile_fields' );

function save_wwt_extra_profile_fields_with_check($userId) {
    if ( !current_user_can( 'edit_user', $userId ) ) {
        return false;
    }

    update_usermeta($userId, SKIP_STOCK_OPERATIONS, $_POST[SKIP_STOCK_OPERATIONS]);
}
add_action( 'personal_options_update', 'save_wwt_extra_profile_fields_with_check' );
add_action( 'edit_user_profile_update', 'save_wwt_extra_profile_fields_with_check' );
