<h1><?php _e('Warehouse material overview', 'woocommerce-warehouse-transactions'); ?></h1>

<?php

$materialList = WWT_MaterialEntity::get_materials();

?>
<div class="insertion">
    <table class="recent">
        <tr>
            <th class="material-name"><?php _e('Material name', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="material-unit"><?php _e('Material unit', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="stock-volume"><?php _e('Stock volume', 'woocommerce-warehouse-transactions'); ?></th>
            <th class="note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></th>
        </tr>

    <?php
        foreach ($materialList as $material) {
            ?>
            <tr>
                <td class="material-name"><?php echo $material->name; ?></td>
                <td class="material-unit"><?php echo $material->unit; ?></td>
                <td class="stock-volume"><?php echo $material->volume; ?></td>
                <td class="note"><?php echo $material->notes; ?></td>
            </tr>
            <?php
        }
    ?>
    </table>
</div>

<style>
    .recent .material-unit {
        width: 100px;
        text-align: left;
    }

    .recent .note {
        width: 450px;
        text-align: left;
    }

    .recent .stock-volume {
        width: 150px;
        text-align: left;
        border: 3px solid black;
    }
</style>
