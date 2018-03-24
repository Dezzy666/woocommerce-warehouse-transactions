<?php

include_once(__DIR__ . '/../objects/wwt-log-entity.php');
include_once(__DIR__ . '/../objects/wwt-material-log-entity.php');
include_once(__DIR__ . '/../objects/wwt-consumption-entity.php');
include_once('admin-page-templates.php');

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

    if ($_POST['product-quantity'] > 0 && isset($_POST['apply-material-change']) && $_POST['apply-material-change']) {
        wwt_create_consumption_log($_POST['product-id'], $_POST['product-quantity'], $userId, $_POST['note']);
    }
}

function wwt_create_consumption_log($productId, $quantity, $userId, $note) {
    $consumptions = WWT_ConsumptionEntity::get_consumptions_for_product($productId);

    foreach ($consumptions as $consumption) {
        $consumptionLog = new WWT_MaterialLogEntity(
            $userId,
            $productId,
            $consumption->volume * $quantity,
            $consumption->materialId,
            $note
        );
        $consumptionLog->save();

        WWT_MaterialEntity::increment_material_quantity($consumption->materialId, -1 * $consumption->volume * $quantity);
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
        <button class="find-button" id="find-button">Find</button>
        <select class="product-select" id="product-id" name="product-id">
            <?php
                foreach ($products as $product) {
                    $wcProduct = wc_get_product($product->ID);
                    if ($wcProduct->get_manage_stock()) {
                        echo '<option value="', $product->ID,'" data-sku="', $wcProduct->get_sku(),'">', apply_filters('wwt_main_page_dropdown_option', $product->ID . ' ' . $product->post_title, $wcProduct),'</option>';
                    }
                }
            ?>
        </select>
        <?php create_insertion_fields("product", true); ?>
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

<div id="dialog" title="<?php _e('Code search', 'woocommerce-warehouse-transactions'); ?>">
  <p><?php _e('Scane a code', 'woocommerce-warehouse-transactions'); ?></p>
  <input value="" id="scanned-code">
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

    jQuery("#dialog").dialog({
      autoOpen: false,
      modal: true
    });
    jQuery("#scanned-code").SimpleBarcodeReadingWrapper({
        onCodeInserted: function(code) {
            jQuery("#dialog").dialog("close");
            jQuery("#product-id option").each(function (element) {
                var jQueryElement = jQuery(this);
                if (jQueryElement.data("sku").toUpperCase() === code) {
                    jQuery(".product-select").val(jQueryElement.val());
                    jQuery(".product-select").trigger('change');
                }
            });
        }
    });
    jQuery("#find-button").click(function(e) {
        e.preventDefault();
        jQuery("#dialog").dialog("open");
    });
</script>
