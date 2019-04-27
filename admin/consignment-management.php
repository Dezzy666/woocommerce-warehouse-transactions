<?php
    include_once(__DIR__ . '/../objects/wwt-consignment-entity.php');
    define('WWT_CONSIGNMENT_SETTING', 'wwt_consignment_setting');

    if (isset($_POST["updatePaymentMethods"])) {
        $consignmentStocks = WWT_ConsignmentEntity::get_all();

        foreach ($consignmentStocks as $consignment) {
            if ($_POST[WWT_CONSIGNMENT_SETTING . $consignment->id]) {
                WWT_ConsignmentEntity::set_payment_methods($consignment->id, $_POST[WWT_CONSIGNMENT_SETTING . $consignment->id]);
            }
        }
    }

?>
<h1><?php _e('Consignment settings', 'woocommerce-warehouse-transactions'); ?></h1>
<div class="insertion">
    <form method="post">
        <?php
            $consignmentStocks = WWT_ConsignmentEntity::get_all();
            $shippingMethods = get_shipping_methods_for_list_selection();

            foreach ($consignmentStocks as $consignment) {
                $selectedPaymentMethods = explode(FIELDS_SEPARATOR, $consignment->paymentMethods);
                ?>
                <div>
                    <h2><?php echo $consignment->name; ?></h2>
                    <p><?php echo $consignment->description; ?></p>
                    <?php print_nonlinked_select_component(WWT_CONSIGNMENT_SETTING . $consignment->id, $shippingMethods, $selectedPaymentMethods); ?>
                </div>
                <?php
            }
        ?>
        <input type="hidden" name="updatePaymentMethods" value="1">
        <?php submit_button(); ?>
    </form>
</div>
