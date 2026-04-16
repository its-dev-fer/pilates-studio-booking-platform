<?php

$versionFile = base_path('version.txt');
$appVersion = is_file($versionFile) ? trim((string) file_get_contents($versionFile)) : null;

return [
    'api_key' => env('BUGSNAG_API_KEY'),
    'app_version' => env('BUGSNAG_APP_VERSION', $appVersion ?: env('APP_VERSION')),
    'release_stage' => env('BUGSNAG_RELEASE_STAGE', env('APP_ENV', 'production')),
    'notify_release_stages' => array_filter(array_map('trim', explode(',', (string) env('BUGSNAG_NOTIFY_RELEASE_STAGES', 'production,staging,local')))),
    'filters' => array_filter(array_map('trim', explode(',', (string) env('BUGSNAG_FILTERS', 'password,password_confirmation,token,authorization,cookie')))),
];
