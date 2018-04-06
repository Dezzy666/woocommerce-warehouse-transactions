<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$isWoocommerceActive = is_plugin_active('woocommerce/woocommerce.php');

if (!function_exists('get_product_tags')) {
    function get_product_tags() {
        $taxonomy     = 'product_tag';
        $orderby      = 'name';
        $show_count   = 1;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 0;      // 1 for yes, 0 for no
        $title        = '';
        $empty        = 0;

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
       );

        $tags = get_terms($taxonomy, $args);

        $tagsArray = array();
        foreach ($tags as $tag) {
            $tagsArray[$tag->slug] = $tag->name;
        }

        return $tagsArray;
    }
}

if (!function_exists('get_product_list')) {
    function get_product_list() {
        $args     = array( 'post_type'   => 'product',
                           'numberposts' => -1,
                           'orderby'     => 'title',
                           'order'       => 'ASC');
        return get_posts( $args );
    }
}

if (!function_exists('get_page_list')) {
    function get_page_list() {
        $pages = get_pages();

        $pagesList = array();

        foreach($pages as $page) {
            $pagesList[$page->ID] = $page->post_title;
        }

        return $pagesList;
    }
}

if (!function_exists('get_product_categories')) {
    function get_product_categories() {
        $taxonomy     = 'product_cat';
        $orderby      = 'name';
        $show_count   = 1;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 0;      // 1 for yes, 0 for no
        $title        = '';
        $empty        = 0;

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
        );

    	return get_categories_select($args);
    }
}

if (!function_exists('get_gateways')) {
    if ($isWoocommerceActive) {
        function get_gateways() {
            global $woocommerce;

            $available_gateways = $woocommerce->payment_gateways;
            $gateways = array();
            foreach ($available_gateways->payment_gateways as $gateway) {
                $gateways[$gateway->id] = $gateway->title;
            }

            return $gateways;
        }
    } else {
        function get_gateways() { return array(); }
    }
}

if (!function_exists('get_shipping_zones')) {
    if ($isWoocommerceActive) {
        function get_shipping_zones() {
            return WC_Shipping_Zones::get_zones();
        }
    } else {
        function get_shipping_zones() { return array(); }
    }
}

if (!function_exists('get_shipping_methods')) {
    if ($isWoocommerceActive) {
        function get_shipping_methods() {
             global $woocommerce;

             return $woocommerce->shipping->load_shipping_methods();
        }
    } else {
        function get_shipping_methods() { return array(); }
    }
}

if (!function_exists('get_payment_gateways')) {
    if ($isWoocommerceActive) {
        function get_payment_gateways() {
             global $woocommerce;

             return $woocommerce->payment_gateways;
        }
    } else {
        function get_payment_gateways() { return array(); }
    }
}

if (!function_exists('get_roles')) {
    function get_roles() {
         global $wp_roles;

         return $wp_roles->get_names();
    }
}

if (!function_exists('get_coupons')) {
    function get_coupons() {
        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => 'shop_coupon',
            'post_status'      => 'publish',
        );

        $coupons = get_posts( $args );

        $couponsNames = array();
        foreach ($coupons as $coupon) {
            $couponsNames[$coupon->ID] = $coupon->post_title;
        }

        return $couponsNames;
    }
}
