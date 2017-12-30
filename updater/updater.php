<?php

require 'vendor/autoload.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/Dezzy666/woocommerce-warehouse-transactions/',
	WWT_PLUGIN_PATH,
	'wwt-update-glug'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
