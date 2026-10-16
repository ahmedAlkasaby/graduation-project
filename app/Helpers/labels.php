<?php

/**
 * Turning a stored value into something a human reads.
 *
 * These back the `*_name` fields in the API resources and the plain-text cells
 * in the admin tables. They all fall back to returning the raw value rather
 * than an empty cell, so a legacy row still shows what it holds.
 */

function labelForValue($type = "")
{
    $array = array(
        // '' => __('None'),
        'today' => __('Today'),
        'tomorrow' => __('Tomorrow'),
        'male' => __('Male'),
        'female' => __('Female'),
        'new' => __('New'),
        'sale' => __('Sale'),
        'offer' => __('Offer'),
        'discount' => __('Discount'),
        'filter' => __('Filter'),
        'special' => __('Special'),
        'house'     => __('House'),
        'work'      => __('Work'),
        'other'     => __('Other'),
        'ar' => __('Arabic'),
        'en' => __('English'),
        'all' => __('All'),
        'clients' => __('Clients'),
        'users' => __('Users'),
        'owner' => __('Owner'),
        'card' => __('Card'),
        'passport' => __('Passport'),
        'checkin' => __('Checkin'),
        'checkout' => __('Checkout'),
        "request" => __('Request'),
        "pending" => __('Pending'),
        "approved" => __('Approved'),
        "preparing" => __('Preparing'),
        "preparing_finished" => __('Preparing Finished'),
        "delivery_go" => __('Delivery Go'),
        "delivered" => __('Delivered'),
        "cancelled" => __('Cancelled'),
        "rejected" => __('Rejected'),
        "returned" => __('Returned'),
        'approve' => __('Approve'),
        'finish' => __('Finish'),
        'delay' => __('Delay'),
        'open' => __('Open'),
        'reject' => __('Reject'),
        'closed' => __('Closed'),
        'not_resolved' => __('Not Resolved'),
        'solved' => __('Solved'),
        'follow_up' => __('Follow Up'),
        'call' => __('Call'),
        'call_back' => __('Call Back'),
        'child'     => __('Child'),
        'son'       => __('Son'),
        'daughter'  => __('Daughter'),
        'wife'      => __('Wife'),
        'husband'   => __('Husband'),
        'father'    => __('Father'),
        'mother'    => __('Mother'),
        'grand_father'    => __('Grand Father'),
        'grand_mother'    => __('Grand Mother'),
        'sister'    => __('Sister'),
        'brother'   => __('Brother'),
        'uncle'    => __('Uncle'),
        'aunt'     => __('Aunt'),
        'cousin'   => __('Cousin'),
        'buyer' => __('Buyer'),
        'personal' => __('Personal'),
        'referral' => __('Referral'),
        'broker' => __('Broker'),
        'tv' => __('TV'),
        'old_data' => __('Old Data'),
        'data' => __('Data'),
        'call_drooped' => __('Call Drooped'),
        'cold_calls' => __('Cold Calls'),
        'client' => __('Client'),
        'empolyee' => __('Empolyee'),
        'email' => __('Email'),
        'meeting' => __('Meeting'),
        'sms' => __('SMS'),
        'in_progress' => __('In Progress'),
        'in_active' => __('In Active'),
        'cancel' => __('Cancel'),
        'branches' => __('Branches'),
        'groups' => __('Groups'),
        'branch' => __('Branch'),
        'group' => __('Group'),
        'reasons' => __('Reason'),
        'active' => __('Active'),
        'unpaid' => __('Unpaid'),
        'paid' => __('Paid'),
        'income' => __('Income'),
        'outcome' => __('Outcome'),
        'cash' => __('Cash'),
        'bank' => __('Bank'),
        'check' => __('Check'),
        'google'  => __('Google'),
        'apple'   => __('Apple'),
        'paypal'  => __('Paypal'),
        'mada'    => __('Mada'),
        'paymob'  => __('Paymob'),
        'vodafone' => __('Vodafone'),
        'we'      => __('We'),
        'orange'  => __('Orange'),
        'etisalat' => __('Etisalat'),
        'fawry'   => __('Fawry'),
        'visa'    => __('Visa'),
        'bank_transfer' => __('Bank Transfer'),
        'master_card' => __('Master Card'),
        'apple_pay' => __('Apple Pay'),
        'google_pay' => __('Google Pay'),
        'text' => __('Text'),
        'select' => __('Select'),
        'datepicker' => __('Date'),
        'number' => __('Number'),
        'image' => __('Image'),
        'file' => __('File'),
        'qualified' => __('Qualified'),
        'fixed' => __('Fixed'),
        'percentage' => __('Percentage'),
        1 => __('Active'),
        0 => __('In Active'),
        'on_delivery' => __('On Delivery'),
        'yes' => __('yes'),
        'no'  => __('No'),
        'planned' => __('Planned'),
        'not_planned' => __('Not Planned'),
        'done' => __('Done'),
        'PATCH' => __("Update"),
        'PUT' => __("Update"),
        'POST' =>  __("Create"),
        'DELETE' => __("Delete"),
        'GET' =>  __("Show"),
        'admin' => __('Admin'),
        'manger' => __('Manger'),
        'view' => __('View'),
        'create' => __('Create'),
        'edit' => __('Edit'),
        'show' => __('Show'),
        'delete' => __('Delete'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return $type;
    }
}

function statusName($type = null)
{
    $array = array(
        -1 => __('All'),
        1 => __('Active'),
        0 => __('In Active'),
    );

    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return $type;
    }
}

function unitName($type = null)
{
    $array = array(
        'kilo'      => __('Kilo'),
        'package'   => __('Package'),
        'packet'    => __('Packet'),
        'cartons'   => __('Cartons'),
        'basket'    => __('Basket'),
        'shawwal'   => __('Shawwal'),
        'intensity' => __('Intensity'),
        'box'       => __('Box'),
        'cork'      => __('Cork'),
        'dish'      => __('Dish'),
        'grain'     => __('Grain'),
        'piece'     => __('Piece'),
        'woodbox'   => __('Wood box'),
        'bag'       => __('Bag'),
        '500gm'     => __('500 Gram'),
        '250gm'     => __('250 Gram'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

function orderName($type = null)
{
    $array = array(
        "request" => __('Request'),
        "pending" => __('Pending'),
        "approved" => __('Approved'),
        "preparing" => __('Preparing'),
        "preparing_finished" => __('Preparing Finished'),
        "delivery_go" => __('Delivery Go'),
        "delivered" => __('Delivered'),
        "cancelled" => __('Cancelled'),
        "rejected" => __('Rejected'),
        "returned" => __('Returned'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

function couponName($type)
{
    $array = array(
        'percentage'    => __('Percentage'),
        'fixed'         => __('Fixed')
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

function userName($type = "")
{
    $array = array(
        'super_admin' => __('Super Admin'),
        'admin' => __('Admin'),
        'sub_admin' => __('Sub Admin'),
        'manger' => __('Manger'),
        'sub_manger' => __('Sub Manger'),
        'account' => __('Account'),
        'account_manger' => __('Account Manger'),
        'account_admin' => __('Account Admin'),
        'call_center' => __('Call Center'),
        'call_center_manger' => __('Call Center Manger'),
        'call_center_admin' => __('Call Center Admin'),
        'office' => __('Office'),
        'office_manger' => __('Office Manger'),
        'office_admin' => __('Office Admin'),
        'client' => __('Client'),
        'delivery' => __('Delivery'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return $type;
    }
}

function branchName($type)
{
    $array = array(
        '' => __('None'),
        'main' => __('Main'),
        'sub' => __('Sub'),
        'other' => __('Other'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return $type;
    }
}

function pageName($type = "")
{
    $array = array(
        'all'        => __('All'),
        'home'       => __('Home'),
        'about'      => __('About'),
        'contact'    => __('Contact'),
        'contact_form' => __('Contact Form'),
        'terms'      => __('Terms'),
        'support'    => __('Support'),
        'privacy'    => __('Privacy'),
        'faq'        => __('Faq'),
        'branch'     => __('Branch'),
        'profile'    => __('Profile'),
        'team'       => __('Team'),
        'mission'    => __('Mission'),
        'vision'     => __('Vision'),
        'value'      => __('Value'),
        'address'    => __('Address'),
        'work_time'  => __('Work Time'),
        'appointment' => __("Appointment"),
        'feature'    => __("Feature"),
        'gift'       => __("Gift"),
        'goal'       => __("Goal"),
        'testimonial' => __("Testimonial"),
        'service'    => __("Service"),
        'post'       => __("Post"),
        'product'    => __("Product"),
        'category'   => __("Category"),
        'copyright'  => __('Copy Right'),
        'social'     => __('Social'),
        'slider'     => __('Slider'),
        'header'     => __('Header'),
        'footer'     => __('footer'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

function hourName($type)
{
    $array = array(
        '00:00:00'    => "00:00",
        '00:30:00'    => "00:30",
        '01:00:00'    => "01:00",
        '01:30:00'    => "01:30",
        '02:00:00'    => "02:00",
        '02:30:00'    => "02:30",
        '03:00:00'    => "03:00",
        '03:30:00'    => "03:30",
        '04:00:00'    => "04:00",
        '04:30:00'    => "04:30",
        '05:00:00'    => "05:00",
        '05:30:00'    => "05:30",
        '06:00:00'    => "06:00",
        '06:30:00'    => "06:30",
        '07:00:00'    => "07:00",
        '07:30:00'    => "07:30",
        '08:00:00'    => "08:00",
        '08:30:00'    => "08:30",
        '09:00:00'    => "09:00",
        '09:30:00'    => "09:30",
        '10:00:00'    => "10:00",
        '10:30:00'    => "10:30",
        '11:00:00'    => "12:00",
        '11:30:00'    => "12:30",
        '12:00:00'    => "12:00",
        '12:30:00'    => "12:30",
        '13:00:00'    => "13:00",
        '13:30:00'    => "13:30",
        '14:00:00'    => "14:00",
        '14:30:00'    => "14:30",
        '15:00:00'    => "15:00",
        '15:30:00'    => "15:30",
        '16:00:00'    => "16:00",
        '16:30:00'    => "16:30",
        '17:00:00'    => "17:00",
        '17:30:00'    => "17:30",
        '18:00:00'    => "18:00",
        '18:30:00'    => "18:30",
        '19:00:00'    => "19:00",
        '19:30:00'    => "19:30",
        '20:00:00'    => "20:00",
        '20:30:00'    => "20:30",
        '21:00:00'    => "21:00",
        '21:30:00'    => "21:30",
        '22:00:00'    => "22:00",
        '22:30:00'    => "22:30",
        '23:00:00'    => "23:00",
        '23:30:00'    => "23:30",
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

function iconName($type)
{
    $array = array(
        'on_delivery' => __('On Delivery'),
        'request' => __('Request'),
        'approved' => __('Approved'),
        'delivered' => __('Delivered'),
        'cancelled' => __('Cancelled'),
        'promo' => __('Promo'),
        'admin' => __('Admin'),
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

function iconColor($type)
{
    $array = array(
        'on_delivery' => '#F0C334',
        'request' => '#495FFF',
        'delivered' => '#4CBB5E',
        'cancelled' => '#EC534A',
        'promo' => '#2AAF7F',
        'admin' => '#2AAF7F',
    );
    if (isset($array[$type])) {
        return $array[$type];
    } else {
        return "";
    }
}

/*
|---------------------------------------------------------------------------
| Logs
|---------------------------------------------------------------------------
*/

/** The HTTP verb an action name is stored as. @see logActionType() */
function logActionName($type)
{
    $array = [
        'Create' => 'POST',
        'Show' => 'GET',
        'Update' => 'PUT',
        'Delete' => 'DELETE',
    ];

    return $array[$type] ?? $type;
}

function logAction($type)
{
    $array = [
        'POST' => __('Create'),
        'GET' => __('Show'),
        'PUT' => __('Update'),
        'PATCH' => __('Update'),
        'DELETE' => __('Delete'),
    ];

    return $array[$type] ?? $type;
}

function logActionColor($type)
{
    $array = [
        'POST' => 'success',
        'GET' => 'info',
        'PUT' => 'warning',
        'PATCH' => 'warning',
        'DELETE' => 'danger',
    ];

    return $array[$type] ?? 'secondary';
}
