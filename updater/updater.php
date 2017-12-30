<?php

require 'vendor/autoload.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/Dezzy666/woocommerce-warehouse-transactions/',
	plugin_dir_path( __FILE__ ) . '/',
	'wwt-update-glug'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');
