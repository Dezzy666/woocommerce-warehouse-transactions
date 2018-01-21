<?php

$recepiesList = WWT_ConsumptionEntity::get_consumptions();

echo "<table>";
echo "<tr>";
echo "<th>" . __('Product name', 'woocommerce-warehouse-transactions') . "</th>";
echo "<th>" . __('Material name', 'woocommerce-warehouse-transactions') . "</th>";
echo "<th>" . __('Needed volume', 'woocommerce-warehouse-transactions') . "</th>";
echo "<th>" . __('Note', 'woocommerce-warehouse-transactions') . "</th>";
echo "</tr>";

foreach ($recepiesList as $recepie) {
    echo "<tr>";

    echo "<td>" . $recepie->productId . "</td>";
    echo "<td>" . $recepie->materialName . "</td>";
    echo "<td>" . $recepie->volume . "</td>";
    echo "<td>" . $recepie->notes . "</td>";

    echo "</tr>";
}

echo "</table>";
