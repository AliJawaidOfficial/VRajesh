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
    return date('D, d M Y h:i A', strtotime($date));
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
