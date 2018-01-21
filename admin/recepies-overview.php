<?php

$materialList = WWT_MaterialEntity::get_materials();

echo "<table>";

foreach ($materialList as $material) {
    echo "<tr>";

    echo "<td>" . $material->name . "</td>";
    echo "<td>" . $material->unit . "</td>";
    echo "<td>" . $material->volume . "</td>";
    echo "<td>" . $material->notes . "</td>";

    echo "</tr>";
}

echo "</table>";
