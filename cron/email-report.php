<?php

function wwt_send_email_with_log_execution() {
    $dayOfMonth = date('d');
    $hour = date('H');
    $startMonth = date('m', strtotime(" -1 months"));
    $startYear = date('Y', strtotime(" -1 months"));
    $endMonth = date('m', strtotime(" -0 months"));
    $endYear = date('Y', strtotime(" -0 months"));

    $uploadDir = wp_upload_dir();
    $logFolder = $uploadDir['basedir'] . '/wwt-log-exports/';

    if (!file_exists($logFolder)) {
        wp_mkdir_p($logFolder);
    }

    $attachementFilePath = $logFolder . '/export' . $startMonth . '-' . $startYear . '_' . $endMonth . '-' . $endYear . '.csv';

    $fileToken = fopen( $attachementFilePath, "w" );
    fwrite($fileToken, 'productName, userName, difference, note,' . "\n");

    $logNodes = WWT_LogEntity::get_log_for_month($startMonth, $startYear, $endMonth, $endYear);

    foreach ($logNodes as $logNode) {
        $productName = apply_filters('wwt_main_page_product_name_column', wwt_get_product_name($logNode->productId), wc_get_product($logNode->productId));
        $userName = wwt_get_user_name($logNode->userId);

        fwrite($fileToken, '"' . $productName . '",');
        fwrite($fileToken, '"' . $userName . '",');
        fwrite($fileToken, '"' . $logNode->difference . '",');
        fwrite($fileToken, '"' . $logNode->notes . '",');
        fwrite($fileToken, '"' . $logNode->insertedAt . '",');
        fwrite($fileToken, "\n");
    }
    fclose( $fileToken );

    $to = get_option(WWT_REPORT_EMAIL);

    if (!isset($to)) return;

    $subject = __('WWT Monthly report', 'woocommerce-warehouse-transactions');
    $body = __('Monthly report containing changes in the warehouse.', 'woocommerce-warehouse-transactions');
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    $headers[] = 'From: WWT export <wwt@medinatur.cz>';

    wp_mail($to, $subject, $body, $headers, $attachementFilePath);
}
add_action('wwt_send_email_with_log', 'wwt_send_email_with_log_execution');
