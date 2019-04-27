<?php
include_once('properties-handling.php');

if (!function_exists('print_select_component')) {
    function print_select_component($mainSign, $selectableUniverse, $key, $alreadySelectedValues = null) {
        if ($alreadySelectedValues == null) {
            $pieces = get_general_property($key);
        } else {
            $pieces = $alreadySelectedValues;
        }

        echo '<div>';
        if ($mainSign != null) {
            echo '<h2>', $mainSign, '</h2>';
        }

        print_nonlinked_select_component($key, $selectableUniverse, $pieces);
        echo '</div>';
    }
}

if (!function_exists('print_nonlinked_select_component')) {
    function print_nonlinked_select_component($key, $selectableUniverse, $selectedValues) {
        echo '<input type="hidden" id="', $key,'" name="', $key,'">';
        echo '<select multiple="multiple" id="', $key,'_select" style="width: 400px;" class="multiselectFields">';
        foreach ($selectableUniverse as $name => $sign) {
            if (in_array($name, $selectedValues)) {
                echo '<option value="',$name,'" selected="selected">',$sign,'</option>';
            } else {
                echo '<option value="',$name,'">',$sign,'</option>';
            }

        }
        echo '</select>';
        ?>
        <input type="button" value="<?php _e('Select all', 'medinatur_v3'); ?>" id="<?php echo $key, '_button'; ?>">
        <script>
            jQuery(function () {
                jQuery('#<?php echo $key; ?>').val(jQuery("#<?php echo $key; ?>_select").val())
                jQuery('#<?php echo $key; ?>_select').select2().on("change", function () {
                    jQuery('#<?php echo $key; ?>').val(jQuery("#<?php echo $key; ?>_select").val())
                });

                jQuery('#<?php echo $key; ?>_button').click(function() {
                    jQuery('#<?php echo $key; ?>_select > option').prop("selected","selected");
                    jQuery('#<?php echo $key; ?>_select').trigger("change");
                })
            });
        </script>
        <?php
    }
}

if (!function_exists('print_input_component')) {
    function print_input_component($mainSign, $key) {
        $option = get_general_property($key, 1, 0);
        echo '<div><h2>', $mainSign, '</h2>';
        echo '<input type="text" id="', $key,'" name="', $key,'" value="',$option,'">';
        echo '</div>';
    }
}

if (!function_exists('print_textarea_component')) {
    function print_textarea_component($mainSign, $key) {
        $option = get_general_property($key, 1, 0);
        echo '<div><h2>', $mainSign, '</h2>';
        echo '<textarea id="', $key, '" name="', $key, '">',$option,'</textarea>';
        echo '</div>';
    }
}

if (!function_exists('print_oneselect_component')) {
    function print_oneselect_component($mainSign, $options, $key) {
        $option = get_general_property($key, 1, 0);
        echo '<div><h2>', $mainSign, '</h2>';
        echo '<select id="', $key,'" name="', $key,'">';
        foreach ($options as $name => $sign) {
            if ($name == $option) {
                echo '<option selected value="', $name,'">', $sign,'</option>';
            } else {
                echo '<option value="', $name,'">', $sign,'</option>';
            }
        }
        echo '</select>';
        echo '</div>';
    }
}

if (!function_exists('print_button_component')) {
    function print_button_component($mainSign, $key) {
        echo '<div>';
        echo '<input type="button" value="', $mainSign ,'" id="', $key, '">';
        echo '</div>';
    }
}
