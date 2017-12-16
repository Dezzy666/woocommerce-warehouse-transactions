<?php

print_button_component(__("Insert WWT CRON", 'woocommerce-warehouse-transactions'), 'insert-wwt-cron');

function print_button_component($mainSign, $key) {
    echo '<div>';
    echo '<input type="button" value="', $mainSign ,'" id="', $key, '">';
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
