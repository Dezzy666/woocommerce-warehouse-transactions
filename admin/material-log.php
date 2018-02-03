<?php

include_once(__DIR__ . '/../objects/wwt-material-entity.php');
include_once(__DIR__ . '/../objects/wwt-material-log-entity.php');
include_once('admin-page-templates.php');

$materials = WWT_MaterialEntity::get_materials();

if(isset($_POST['material-quantity']) && isset($_POST['material-id'])) {
    $userId = get_current_user_id();

    if ( is_numeric($_POST['material-quantity'])) {
        wwt_update_material_stock($_POST['material-id'], $_POST['material-quantity']);
        $newLog = new WWT_MaterialLogEntity($userId, NULL, $_POST['material-quantity'], $_POST['material-id'], $_POST['note']);
        $newLog->save();
    }
}

function wwt_update_material_stock($product, $quantity) {

}

?>
<h1><?php _e('Warehouse material movement log', 'woocommerce-warehouse-transactions'); ?></h1>
<div class="insertion">
    <form method="post">
        <h2><?php _e('Insert change', 'woocommerce-warehouse-transactions'); ?></h2>
        <select class="material-select" id="material-id" name="material-id">
            <?php
                foreach ($materials as $material) {
                    echo '<option value="', $material->id,'"">', apply_filters('wwt_material_dropdown_option', $material->name, $material),'</option>';
                }
            ?>
        </select>
        <?php create_insertion_fields("material"); ?>
    </form>
</div>

<div class="insertion">
    <h2><?php _e('Last changes', 'woocommerce-warehouse-transactions'); ?> </h2>
    <table class="recent">
        <tr>
            <th class="material-name"><?php _e('Material name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="user-name"><?php _e('User name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="product-name"><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="difference"><?php _e('Difference', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="inserted-at"><?php _e('Inserted at', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>
        <?php
            $logNodes = wwt_material_log_transformer(WWT_MaterialLogEntity::get_last());
            foreach ($logNodes as $logNode) {
                echo '<tr class="data-content">',
                          '<td class="material-name">', $logNode["material-name"],
                    '</td><td class="user-name">', $logNode["user-name"],
                    '</td><td class="product-name">', $logNode["product-name"],
                    '</td><td class="difference">', $logNode["difference"],
                    '</td><td class="note">', $logNode["note"],
                    '</td><td class="inserted-at">', $logNode["inserted-at"],
                    '</td></tr>';
            }
        ?>
    </table>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery(".material-select").select2();
  });
</script>
