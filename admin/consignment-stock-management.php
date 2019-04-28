<?php

include_once('ap-warehouse-toolkit.php');
include_once('admin-page-templates.php');


if(isset($_POST['product-quantity']) && isset($_POST['product-id'])) {
    $userId = get_current_user_id();

    $product = wc_get_product($_POST['product-id']);

    if ($product && is_numeric($_POST['product-quantity']) && is_numeric($_POST['consignment-id'])) {
        wwt_save_consignment_stock_change($_POST['consignment-id'], $product, $_POST['product-id'], $_POST['product-quantity'], $_POST['note']);
    }

    if ($_POST['product-quantity'] > 0 && isset($_POST['apply-material-change']) && $_POST['apply-material-change']) {
        wwt_create_consumption_log($_POST['product-id'], $_POST['product-quantity'], $userId, $_POST['note']);
    }
}

?>
<h1><?php _e('Consignment stock movement log', 'woocommerce-warehouse-transactions'); ?></h1>
<div class="insertion">
    <h2><?php _e('Insert change', 'woocommerce-warehouse-transactions'); ?></h2>
    <button class="find-button" id="find-button"><?php _e('Find', 'woocommerce-warehouse-transactions'); ?></button>
    <form method="post">
        <?php create_select_for_consignment_stocks(); ?>
        <?php create_select_for_products(); ?>
        <?php create_insertion_fields("product", true); ?>
    </form>
</div>

<div class="insertion">
    <h2><?php _e('Last changes', 'woocommerce-warehouse-transactions'); ?> </h2>
    <table class="recent">
        <tr>
            <th class="consignment-name"><?php _e('Consignment name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="product-name"><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="user-name"><?php _e('User name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="difference"><?php _e('Difference', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="inserted-at"><?php _e('Inserted at', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>
        <?php
            $logNodes = wwt_log_transformer(WWT_ConsignmentLogEntity::get_last());
            foreach ($logNodes as $logNode) {
                echo '<tr class="data-content">',
                    '<td class="consignment-name">', $logNode["consignment-name"],
                    '</td><td class="product-name">', $logNode["product-name"],
                    '</td><td class="user-name">', $logNode["user-name"],
                    '</td><td class="difference">', $logNode["difference"],
                    '</td><td class="note">', $logNode["note"],
                    '</td><td class="inserted-at">', $logNode["inserted-at"],
                    '</td></tr>';
            }

            //create_paging_buttons(4);
        ?>

    </table>
</div>

<?php create_code_search_dialog(); ?>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".product-select").select2();
        jQuery(".consignment-select").select2();
    });
</script>

<?php //repaint_standard_stock_log(); ?>
