<?php

include_once(__DIR__ . '/../objects/wwt-material-entity.php');
include_once('admin-page-templates.php');

$materials = WWT_MaterialEntity::get_materials();

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

<script type="text/javascript">
    jQuery(document).ready(function() {
      jQuery(".material-select").select2();
  });
</script>
