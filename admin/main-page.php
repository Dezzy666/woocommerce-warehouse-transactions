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
            <th class="inserted-at"><?php _e('Inserted at', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>
        <?php
            $logNodes = wwt_log_transformer(WWT_LogEntity::get_last());
            foreach ($logNodes as $logNode) {
                echo '<tr class="data-content"><td class="product-name">', $logNode["product-name"],
                    '</td><td class="user-name">', $logNode["user-name"],
                    '</td><td class="difference">', $logNode["difference"],
                    '</td><td class="note">', $logNode["note"],
                    '</td><td class="inserted-at">', $logNode["inserted-at"],
                    '</td></tr>';
            }
        ?>
        <tr class="last">
            <td class="newer"><input type="button" id="wwt-newer" value="< <?php _e('Newer', 'woocommerce-warehouse-transactions');?>"></td>
            <td colspan="3"></td>
            <td class="older"><input type="button" id="wwt-older" value="<?php _e('Older', 'woocommerce-warehouse-transactions');?> >"></td>
        <tr>
    </table>
    <input type="hidden" id="wwt-page" value="0">
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery(".product-select").select2();

      jQuery("#wwt-older").click(function () {
          var newPage = parseInt(jQuery("#wwt-page").val()) + 1;
          changePage(newPage);
      });

      jQuery("#wwt-newer").click(function () {
          var newPage = parseInt(jQuery("#wwt-page").val()) - 1;
          if (newPage < 0) return;
          changePage(newPage);
      });
    });

    var changePage = function (newPage) {
        var data = {
            'action': 'wwt_get_data_page',
            'page': newPage
        };
        jQuery(".recent").addClass("disabled");

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            var responseData = JSON.parse(response);
            redrawTableContent(responseData.data);
            jQuery(".recent").removeClass("disabled");
        });

        jQuery("#wwt-page").val(newPage);
    }

    var redrawTableContent = function (newData) {
        jQuery(".data-content").remove();
        newData.forEach(function (rowData) {
            var row = jQuery('<tr class="data-content">');
            row.append('<td class="product-name">' + rowData["product-name"] + '</td>');
            row.append('<td class="user-name">' + rowData["user-name"] + '</td>');
            row.append('<td class="difference">' + rowData["difference"] + '</td>');
            row.append('<td class="note">' + rowData["note"] + '</td>');
            row.append('<td class="inserted-at">' + rowData["inserted-at"] + '</td>');

            row.insertBefore(jQuery(".last"));
        });
    };
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

    .disabled {
        opacity: .5;
    }

    .product-select {
        width: 350px;
    }

    .recent .product-name {
        width: 250px;
        text-align: left;
    }

    .recent .last .newer{
        text-align: left;
    }

    .recent .last .older {
        text-align: right;
    }

    .recent .last .newer input, .recent .last .older input {
        width: 100px;
        border: 2px solid black;
        background-color: #87b5ff;
        border-radius: 3px;
        cursor: pointer;
    }

    .recent .last .newer input:hover, .recent .last .older input:hover {
        background-color: #5a98fc;
    }

    .recent .last .newer input:active, .recent .last .older input:active {
        background-color: #59fc6c;
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

    .recent .inserted-at {
        width: 150px;
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
