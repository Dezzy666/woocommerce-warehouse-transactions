<?php

function create_insertion_fields($prefix, $showMaterialCheckbox = false, $showMainStoreCheckbox = false) {
    ?>
    <label for="<?php echo $prefix; ?>-quantity"><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></label><input type="number" id="<?php echo $prefix; ?>-quantity" name="<?php echo $prefix; ?>-quantity">
    <label for="<?php echo $prefix; ?>-note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></label><input type="text" id="note" name="note">

    <?php if ($showMaterialCheckbox) { ?>
        <input type="checkbox" name="apply-material-change" id="apply-material-change">
        <label for="apply-material-change"><?php _e('Apply material changes', 'woocommerce-warehouse-transactions'); ?></label>
    <?php } ?>

    <?php if ($showMainStoreCheckbox) { ?>
        <input type="checkbox" name="take-from-main-store" id="take-from-main-store">
        <label for="take-from-main-store"><?php _e('Take from main store', 'woocommerce-warehouse-transactions'); ?></label>
    <?php } ?>

    <div>
        <?php _e('+ value means added, - value means taken', 'woocommerce-warehouse-transactions'); ?>
    </div>
    <?php submit_button(__('Insert', 'woocommerce-warehouse-transactions')); ?>
    <?php
}

function create_select_for_consignment_stocks() {
    ?>
    <select class="consignment-select" id="consignment-id" name="consignment-id">
    <?php
        $consignments = WWT_ConsignmentEntity::get_all();

        foreach ($consignments as $consignment) {
            echo '<option value="', $consignment->id,'">', apply_filters('wwt_main_page_dropdown_option_consignment', $consignment->name, $consignment),'</option>';
        }
    ?>
    </select>
    <?php
}

function create_select_for_products() {
    ?>
    <select class="product-select" id="product-id" name="product-id">
    <?php
        $isCeskeSluzbyUp = is_plugin_active('ceske-sluzby/ceske-sluzby.php');
        $products = get_product_list();

        foreach ($products as $product) {
            $wcProduct = wc_get_product($product->ID);
            if ($wcProduct->get_manage_stock()) {
                if ($isCeskeSluzbyUp) {
                    //// USE EAN
                    $searchValue = get_post_meta($product->ID, 'ceske_sluzby_hodnota_ean', true);
                } else {
                    //// USE SKU
                    $searchValue = $wcProduct->get_sku();
                }

                echo '<option value="', $product->ID,'" data-sku="', $searchValue,'">', apply_filters('wwt_main_page_dropdown_option', $product->ID . ' ' . $product->post_title, $wcProduct),'</option>';
            }
        }
    ?>
    </select>
    <?php
}

function create_code_search_dialog() {
    ?>
    <div id="dialog" title="<?php _e('Code search', 'woocommerce-warehouse-transactions'); ?>">
        <p><?php _e('Scane a code', 'woocommerce-warehouse-transactions'); ?></p>
        <input value="" id="scanned-code">
    </div>

    <script type="text/javascript">
        jQuery("#dialog").dialog({
        autoOpen: false,
        modal: true
        });
        jQuery("#scanned-code").SimpleBarcodeReadingWrapper({
            onCodeInserted: function(code) {
                jQuery("#dialog").dialog("close");

                var valid = false;
                jQuery("#product-id option").each(function (element) {
                    var jQueryElement = jQuery(this);
                    if (String(jQueryElement.data("sku")).toUpperCase() === code) {
                        jQuery(".product-select").val(jQueryElement.val());
                        jQuery(".product-select").trigger('change');
                        return valid = true;
                    }
                });

                if (!valid) {
                    alert("<?php _e('Code not recognized', 'woocommerce-warehouse-transactions'); ?>");
                }
            }
        });
        jQuery("#find-button").click(function(e) {
            e.preventDefault();
            jQuery("#dialog").dialog("open");
        });
    </script>
    <?php
}

function create_paging_buttons($colspan) {
    ?>
        <tr class="last">
            <td class="newer"><input type="button" id="wwt-newer" value="< <?php _e('Newer', 'woocommerce-warehouse-transactions');?>"></td>
            <td colspan="<?php echo $colspan; ?>"></td>
            <td class="older"><input type="button" id="wwt-older" value="<?php _e('Older', 'woocommerce-warehouse-transactions');?> >"></td>
        <tr>
        <input type="hidden" id="wwt-page" value="0">
        <script type="text/javascript">

            jQuery(document).ready(function() {
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
        </script>
    <?php
}

function repaint_standard_stock_log() {
    ?>
    <script type="text/javascript">
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
    <?php
}
