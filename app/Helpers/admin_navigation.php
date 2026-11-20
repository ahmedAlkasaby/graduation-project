<?php

/**
 * Names the admin panel needs to route and label itself: which route prefix a
 * user type belongs under, and what to call a database table on screen.
 */

function getRouteName($route_type = "")
{
    $array = array(
        'admin' => 'admin',
        'client' => 'client',
        'employee' => 'employee',
        'facilty' => 'facilty',
    );
    if (isset($array[$route_type])) {
        return $array[$route_type];
    } else {
        return "admin";
    }
}

function getModuleTableModelName($type)
{
    $array = array(
        'activity_logs' => 'Activity Log',
        'additions' => 'Addition',
        'actions' => 'Action',
        'addresses' => 'Address',
        'branches' => 'Branch',
        'brands' => 'Brand',
        'carts' => 'Cart',
        'categories' => 'Category',
        'currencies' => 'Currency',
        'countries' => 'Country',
        'cities' => 'City',
        'regions' => 'Region',
        'contacts' => 'Contact',
        'coupons' => 'Coupon',
        'groups' => 'Group',
        'reviews' => 'Review',
        'favorites' => 'Favorite',
        'wallets' => 'Wallet',
        'points' => 'Point',
        'units' => 'Unit',
        'users' => 'User',
        'user_codes' => 'UserCode',
        'payments' => 'Payment',
        'products' => 'Product',
        'pages' => 'Page',
        'posts' => 'Post',
        'orders' => 'Order',
        'order_rejects' => 'Order Reject',
        'delivery_times' => 'Delivery Time',
        'logs' => 'Log',
        'settings' => 'Setting',
        'services' => 'Service',
        'tags' => 'Tag',
        'roles' => 'Role',
        'translations' => 'Translation',
        'tables' => 'Table View',
        'images' => 'Image',
        'notifications' => 'Notification',
        'dashboard' => 'Dashboard',
        'statistics' => 'Statistics',
        'order' => 'Order',
        'admin' => 'Admin',
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return $type;
    }
}

function notificationModelName($type = "")
{
    $array = array(
        'admin' => "AdminNotification",
        'orders' => "OrderNotification",
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return $array["admin"];
    }
}
