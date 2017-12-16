<?php

print_button_component(__("Insert WWT CRON", 'woocommerce-warehouse-transactions'), 'insert-wwt-cron');

?>
<form class="eet-settings" action="options.php" method="post">
    <?php settings_fields( WWT_SETTING_GROUP ); ?>
    <?php do_settings_sections( WWT_SETTING_GROUP ); ?>

    <?php
    print_input_component(__("Email the report is send to"), WWT_REPORT_EMAIL);
    ?>

    <?php submit_button(); ?>
</form>

<?php
function print_button_component($mainSign, $key) {
    echo '<div>';
    echo '<input type="button" value="', $mainSign ,'" id="', $key, '">';
    echo '</div>';
}

function print_input_component($mainSign, $key) {
    $option = get_option($key);
    echo '<div><h2>', $mainSign, '</h2>';
    echo '<input type="text" id="', $key,'" name="', $key,'" value="',$option,'">';
    echo '</div>';
}

?>
<script type="text/javascript">
jQuery('#insert-wwt-cron').click(function () {
    var data = {
        'action': 'wwt_insert_cron'
    };
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(ajaxurl, data, function(response) {
        alert(response);
    });
});
</script>
