<?php

$materialList = WWT_MaterialEntity::get_materials();

echo "<table>";
echo "<tr>";
echo "<th>" . __('Material name', 'woocommerce-warehouse-transactions') . "</th>";
echo "<th>" . __('Material unit', 'woocommerce-warehouse-transactions') . "</th>";
echo "<th>" . __('Stock volume', 'woocommerce-warehouse-transactions') . "</th>";
echo "<th>" . __('Note', 'woocommerce-warehouse-transactions') . "</th>";
echo "</tr>";

foreach ($materialList as $material) {
    echo "<tr>";

    echo "<td>" . $material->name . "</td>";
    echo "<td>" . $material->unit . "</td>";
    echo "<td>" . $material->volume . "</td>";
    echo "<td>" . $material->notes . "</td>";

    echo "</tr>";
}

echo "</table>";
