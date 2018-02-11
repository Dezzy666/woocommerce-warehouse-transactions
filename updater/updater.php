<?php

require 'vendor/autoload.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://updates.medinatur.cz/woocommerce-warehouse-transactions/next-version.json',
	WWT_PLUGIN_PATH,
	'woocommerce-warehouse-transactions'
);
