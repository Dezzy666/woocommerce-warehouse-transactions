<?php
if (!defined('FIELDS_SEPARATOR')) {
    define('FIELDS_SEPARATOR', ',');
}

if (!function_exists('get_general_property')) {
    function get_general_property($key, $isOverridable = 1, $parseToArray = 1, $defaultValue = "") {
        $property = get_option($key, $defaultValue);

        if ($isOverridable && defined($key)) {
            $property = constant($key);
        }

        if ($parseToArray) {
            return explode(FIELDS_SEPARATOR, $property);
        }

        return $property;
    }
}

if (!function_exists('null_check_and_separate')) {
    function null_check_and_separate($propertyContent) {
        if ($propertyContent == null || $propertyContent == "") return [];
        return explode(FIELDS_SEPARATOR, $propertyContent);
    }
}

if (!function_exists('get_user_array_meta')) {
    function get_user_array_meta($userId, $propertyId) {
        $propertyContent = get_user_meta( $userId, $propertyId, true);
        return null_check_and_separate($propertyContent);
    }
}

if (!function_exists('update_user_array_meta')) {
    function update_user_array_meta($userId, $propertyId, $value) {
        if ($value != null && is_array($value)) {
            update_usermeta($userId, $propertyId, implode(FIELDS_SEPARATOR, $value));
        } else if ($value != null && is_string($value)) {
            update_usermeta($userId, $propertyId, $value);
        } else {
            update_usermeta($userId, $propertyId, "");
        }
    }
}

if (!function_exists('get_post_array_meta')) {
    function get_post_array_meta($postId, $propertyId) {
        $propertyContent = get_post_meta($postId, $propertyId, true);
        return null_check_and_separate($propertyContent);
    }
}

if (!function_exists('update_post_array_meta')) {
    function update_post_array_meta($postId, $propertyId, $value) {
        if ($value != null && is_array($value)) {
            update_post_meta($postId, $propertyId, implode(FIELDS_SEPARATOR, $value));
        } else if ($value != null && is_string($value)) {
            update_post_meta($postId, $propertyId, $value);
        } else {
            update_post_meta($postId, $propertyId, "");
        }
    }
}
