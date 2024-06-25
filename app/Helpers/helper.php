<?php

use Carbon\Carbon;

/**
 * Date & Time
 * Date Time Format
 * Date Format
 * Time Ago
 */
function standardDateTimeFormat($date)
{
    return date('d/M/Y h:i A', strtotime($date));
}

function dateTimeFormat($date)
{
    return date('Y-m-d h:i A', strtotime($date));
}

function dateFormat($date)
{
    return date('Y-m-d', strtotime($date));
}

function dateMonthYearFormat($date)
{
    return date('M Y', strtotime($date));
}

function dateYearFormat($date)
{
    return date('Y', strtotime($date));
}

function timeAgo($dateTime)
{
    $date = Carbon::parse($dateTime);
    $now = Carbon::now();
    $diff = $date->diffInSeconds($now);

    if ($diff < 60) {
        return $diff . ' second' . ($diff === 1 ? '' : 's') . ' ago';
    } elseif ($diff < 3600) {
        $minutes = $date->diffInMinutes($now);
        return $minutes . ' minute' . ($minutes === 1 ? '' : 's') . ' ago';
    } elseif ($diff < 86400) {
        $hours = $date->diffInHours($now);
        return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago';
    } elseif ($diff < 604800) {
        $days = $date->diffInDays($now);
        return $days . ' day' . ($days === 1 ? '' : 's') . ' ago';
    } elseif ($diff < 2419200) {
        $weeks = $date->diffInWeeks($now);
        return $weeks . ' week' . ($weeks === 1 ? '' : 's') . ' ago';
    } else {
        $years = $date->diffInYears($now);
        return $years . ' year' . ($years === 1 ? '' : 's') . ' ago';
    }
}


// Get Country And Timezone
function getCountryAndTimezone($ip)
{
    $response = file_get_contents("http://ip-api.com/json/{$ip}");
    $data = json_decode($response, true);

    if ($data['status'] === 'success') {
        return [
            'country' => $data['country'],
            'timezone' => $data['timezone']
        ];
    } else {
        return [
            'country' => 'Pakistan',
            'timezone' => 'Asia/Karachi'
        ];
    }
}

// Convert Timezone To UTC
function convertTimeToUtc($time, $timezone)
{
    $date = new DateTime($time, new DateTimeZone($timezone));
    $date->setTimezone(new DateTimeZone('UTC'));
    return $date->format('H:i:s');
}

function convertUTCToLocalTime($dateTimeString, $timezone)
{
    $date = new DateTime($dateTimeString, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('Y-m-d H:i:s');
}
