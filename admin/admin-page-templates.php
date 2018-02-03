<?php

function create_insertion_fields($prefix, $showCheckbox = false) {
    ?>
    <label for="<?php echo $prefix; ?>-quantity"><?php _e('Quantity', 'woocommerce-warehouse-transactions'); ?></label><input type="number" id="<?php echo $prefix; ?>-quantity" name="<?php echo $prefix; ?>-quantity">
    <label for="<?php echo $prefix; ?>-note"><?php _e('Note', 'woocommerce-warehouse-transactions'); ?></label><input type="text" id="note" name="note">

    <?php if ($showCheckbox) { ?>
        <input type="checkbox" name="apply-material-change" id="apply-material-change" checked="checked">
        <label for="apply-material-change"><?php _e('Apply material changes', 'woocommerce-warehouse-transactions'); ?></label>
    <?php } ?>
    <div>
        <?php _e('+ value means added, - value means taken', 'woocommerce-warehouse-transactions'); ?>
    </div>
    <?php submit_button(__('Insert', 'woocommerce-warehouse-transactions')); ?>
    <?php
}
