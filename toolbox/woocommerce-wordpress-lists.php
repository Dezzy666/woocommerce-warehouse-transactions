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

if (!function_exists('get_shipping_method_with_id')) {
    function get_shipping_method_with_id($order) {
        $shippingMethod = array_values($order->get_items( 'shipping'))[0]['method_id'];
        $shippingMethodInstanceId = array_values($order->get_items( 'shipping'))[0]['instance_id'];

        $shippingMethod = str_replace(":", "_", $shippingMethod);

        if ($shippingMethodInstanceId != 0 && strpos($shippingMethod, $shippingMethodInstanceId) === FALSE) {
            $shippingMethod .= '_' . $shippingMethodInstanceId;
        }

        return $shippingMethod;
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

if (!function_exists('get_orders_ids_in_state')) {
    function get_orders_ids_in_state($states) {
        $wordpressStates = array();

        foreach ($states as $state) {
            array_push($wordpressStates, 'wc-'.$state);
        }

        $args     = array( 'post_type'   => 'shop_order',
                           'post_status' => $wordpressStates,
                           'numberposts' => -1,
                           'orderby'     => 'title',
                           'order'       => 'ASC');

        $orders = get_posts( $args );
        $output = array();

        foreach ($orders as $order) {
            $output[$order->ID] = str_replace('wc-', '', $order->post_status);
        }

        return $output;
    }
}

if (!function_exists('get_order_notes')) {
    function get_order_notes($order_id) {
        return wc_get_order_notes( array( 'order_id' => $order_id ) );
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

if (!function_exists('get_shipping_methods_for_list_selection')) {
    if ($isWoocommerceActive) {
        function get_shipping_methods_for_list_selection() {
             $shippingMethods = get_shipping_methods();
             $availableShippingZones = get_shipping_zones();

             $shippingMethodsList = array();

             foreach ($availableShippingZones as $shippingZone) {
                 foreach ($shippingZone["shipping_methods"] as $shippingMethod) {
                     $shippingMethodsList[$shippingMethod->id . '_' . $shippingMethod->instance_id] = $shippingMethod->title . ' (' . $shippingMethod->id . ':' . $shippingMethod->instance_id . ')';
                 }
             }

             foreach($shippingMethods as $shippingMethod) {
                 if ($shippingMethod->id == 'flat_rate' || $shippingMethod->id == 'free_shipping' || $shippingMethod->id == 'local_pickup') continue;
                 $shippingMethodsList[$shippingMethod->id] = $shippingMethod->title;
             }

             return $shippingMethodsList;
        }
    } else {
        function get_shipping_methods_for_list_selection() { return array(); }
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

if (!function_exists('get_users_with_roles')) {
    function get_users_with_roles($roles) {
        $args = array(
            'role__in'     => $roles
         );
         return get_users($args);
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

if (!function_exists('woo_cart_has_all_virtual_products')) {
    if ($isWoocommerceActive) {
        function woo_cart_has_all_virtual_products() {
            $products = WC()->cart->get_cart();

            foreach( $products as $product ) {
              $product_id = $product['product_id'];
              $is_virtual = get_post_meta( $product_id, '￼_virtual', true );

              if( $is_virtual != 'yes' )
                return false;
            }

            return true;
        }
    } else {
        function woo_cart_has_all_virtual_products() { return false; }
    }
}

if (!function_exists('woo_cart_has_all_giftcoupon_products')) {
    if ($isWoocommerceActive) {
        function woo_cart_has_all_giftcoupon_products() {
            $products = WC()->cart->get_cart();

            foreach( $products as $product ) {
              $product_id = $product['product_id'];
              $is_coupon = get_post_meta( $product_id, 'giftcoupon', true );

              if( $is_coupon != 'yes' )
                return false;
            }

            return true;
        }
    } else {
        function woo_cart_has_all_giftcoupon_products() { return false; }
    }
}
