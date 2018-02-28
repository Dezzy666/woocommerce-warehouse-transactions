
<h1><?php _e('Warehouse recepies', 'woocommerce-warehouse-transactions'); ?></h1>
<?php

$recepiesList = WWT_ConsumptionEntity::get_consumptions();

?>
<div class="insertion">
    <table class="recent">
        <tr>
            <th class="product-name"><?php _e('Product name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="material-name"><?php _e('Material name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="needed-volume"><?php _e('Needed volume', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>
    <?php
        foreach ($recepiesList as $recepie) {
            ?>
            <tr>
                <?php
                    $product = wc_get_product($recepie->productId);
                ?>

                <td class="product-name"><?php echo apply_filters('wwt_recepies_product_name', $product->name, $product); ?></td>
                <td class="material-name"><?php echo $recepie->materialName; ?></td>
                <td class="needed-volume"><?php echo $recepie->volume; ?></td>
                <td class="note"><?php echo $recepie->notes; ?></td>
            </tr>
        <?php
        }
    ?>
    </table>
</div>

<style>
    .recent .product-name {
        width: 150px;
        text-align: left;
    }

    .recent .note {
        width: 450px;
        text-align: left;
    }

    .recent .needed-volume {
        width: 150px;
        text-align: left;
    }
</style>
