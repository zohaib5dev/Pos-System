<?php

use App\Models\ActivityLog;
use App\Models\BusinessSetting;

function amo($amount)
{
    $settings = BusinessSetting::first();

    return $settings->currency_symbol . ' ' . number_format($amount, 2);
}

function dateFormat()
{
    $settings = BusinessSetting::first();

    return $settings->date_format;
}

function timeFormat()
{
    $settings = BusinessSetting::first();

    return $settings->time_format;
}

function site()
{
    return BusinessSetting::first();
}

function getLogo()
{
    $settings = BusinessSetting::first();
    if ($settings->business_logo && file_exists(public_path('assets/img/' . $settings->business_logo))) {
        return asset('assets/img/' . $settings->business_logo);
    }

    if (file_exists(public_path('assets/img/logo.png'))) {
        return asset('assets/img/logo.png');
    }

    return null;
}

function logActivity($action, $model, $oldValues = [], $newValues = [])
{
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => $action,
        'model_type' => get_class($model),
        'model_id' => $model->id,
        'old_values' => !empty($oldValues) ? json_encode($oldValues) : null,
        'new_values' => !empty($newValues) ? json_encode($newValues) : null,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}
