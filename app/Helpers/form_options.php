<?php

use App\Enums\LanguageEnum;
use App\Enums\LoginMethodEnum;
use App\Enums\NotificationChannelEnum;
use App\Enums\UserTypeEnum;

/**
 * Option lists the dashboard API serves for its select fields.
 *
 * Each returns `value => translated label`. Closed value sets live in
 * app/Enums; the functions here are thin adapters over them. The rest are
 * UI-only lists (yes/no toggles, page sizes) with no domain meaning.
 *
 * A project adds its own lists at the bottom.
 */

/**
 * The user types a form may offer. Only an admin is offered the admin type,
 * so nobody can promote someone past themselves.
 */
function userType($all = 'yes', $is_search = 'no')
{
    $array = auth()->user()?->isAdmin()
        ? UserTypeEnum::options()
        : UserTypeEnum::optionsOnly(UserTypeEnum::appTypes());

    return $is_search === 'yes' ? ['' => __('None')] + $array : $array;
}

function userTypeDefault($all = 'yes')
{
    return $all === 'yes'
        ? UserTypeEnum::optionsWithPlaceholder(__('None'))
        : UserTypeEnum::options();
}

/**
 * Page sizes. The larger ones are only offered to users allowed to pull big
 * exports, since they are what make a list query expensive.
 */
function perPageOptions($all = 'no', $other = 'yes')
{
    $array = [
        25 => '25',
        50 => '50',
        100 => '100',
        250 => '250',
    ];

    if ($other === 'yes' && auth()->user()?->can('tables.index')) {
        $array += [
            500 => '500',
            1000 => '1000',
            2500 => '2500',
            5000 => '5000',
            10000 => '10000',
        ];
    }

    if ($all !== 'yes') {
        unset($array[0]);
    }

    return $array;
}

/** Half-hour slots, for any form that schedules something. */
function hourType($all = 'yes')
{
    $array = ['' => __('Choose Hour')];

    for ($hour = 0; $hour < 24; $hour++) {
        foreach (['00', '30'] as $minute) {
            $key = sprintf('%02d:%s:00', $hour, $minute);
            $array[$key] = sprintf('%02d:%s', $hour, $minute);
        }
    }

    if ($all === 'no') {
        unset($array['']);
    }

    return $array;
}

/** Which images the "site images" screen lets you replace. */
function imageType()
{
    return [
        'logo' => __('Logo'),
        'authlogo' => __('Auth Logo'),
        'login' => __('Login'),
        'register' => __('Register'),
        'pass' => __('Password'),
        'notfound' => __('Not Found'),
        'notfoundpage' => __('Not Found Page'),
        'emptynotification' => __('Empty Notification'),
        'app_splash' => __('App Splash'),
        'share' => __('Share Image'),
    ];
}

/*
|---------------------------------------------------------------------------
| Yes / no and active / inactive
|---------------------------------------------------------------------------
| -1 means "any" and is only ever used by a filter, never saved.
*/

function statusType($all = 'no')
{
    $array = [
        -1 => __('Choose Status'),
        1 => __('Active'),
        0 => __('In Active'),
    ];

    if ($all === 'no') {
        unset($array[-1]);
    }

    return $array;
}

function statusDefaultType()
{
    return [
        0 => __('In Active'),
        1 => __('Active'),
    ];
}

function statusShow($all = 'no')
{
    $array = [
        -1 => __('All'),
        1 => __('Yes'),
        0 => __('No'),
    ];

    if ($all === 'no') {
        unset($array[-1]);
    }

    return $array;
}

function statusShowDefault($all = 'no')
{
    $array = [
        -1 => __('All'),
        0 => __('No'),
        1 => __('Yes'),
    ];

    if ($all === 'no') {
        unset($array[-1]);
    }

    return $array;
}

function statusShowType()
{
    return [
        0 => __('No'),
        1 => __('Yes'),
    ];
}

function showType($all = 'no')
{
    $array = [
        '' => __('All'),
        'yes' => __('Yes'),
        'no' => __('No'),
    ];

    if ($all === 'no') {
        unset($array['']);
    }

    return $array;
}

function genderType($all = 'no')
{
    $array = [
        '' => __('None'),
        'male' => __('Male'),
        'female' => __('Female'),
    ];

    if ($all === 'no') {
        unset($array['']);
    }

    return $array;
}

function themeType()
{
    return [
        'light' => __('Light'),
        'dark' => __('Dark'),
        'semi-dark' => __('Semi Dark'),
    ];
}

/*
|---------------------------------------------------------------------------
| Enum-backed lists
|---------------------------------------------------------------------------
*/

/** @see \App\Enums\LanguageEnum */
function languageType()
{
    return LanguageEnum::options();
}

/** @see \App\Enums\LoginMethodEnum */
function primaryLoginType()
{
    return LoginMethodEnum::options();
}

/** @see \App\Enums\NotificationChannelEnum */
function notificationChannels()
{
    return NotificationChannelEnum::options();
}


function logActionType($all = 'yes')
{
    $array = [
        '' => __('All'),
        'Create' => __('Create'),
        'Show' => __('Show'),
        'Update' => __('Update'),
        'Delete' => __('Delete'),
    ];

    if ($all !== 'yes') {
        unset($array['']);
    }

    return $array;
}

function logTypeOptions($all = 'yes')
{
    $array = [
        '' => __('All'),
        'admin' => __('Admin'),
        'api' => __('API'),
    ];

    if ($all !== 'yes') {
        unset($array['']);
    }

    return $array;
}
