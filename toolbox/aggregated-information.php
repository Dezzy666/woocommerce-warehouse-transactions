<?php

if (!function_exists('get_current_user_role')) {
    function get_current_user_role() {
        $currentUser = wp_get_current_user();
        if ($currentUser->ID == 0) return false;

        return $currentUser->roles[0];
    }
}
