<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Prepare writable bootstrap cache directory in /tmp
$bootstrapCache = '/tmp/bootstrap/cache';
if (!is_dir($bootstrapCache)) {
    mkdir($bootstrapCache, 0777, true);
}

if (file_exists(__DIR__ . '/../bootstrap/cache/packages.php')) {
    @copy(__DIR__ . '/../bootstrap/cache/packages.php', $bootstrapCache . '/packages.php');
}
if (file_exists(__DIR__ . '/../bootstrap/cache/services.php')) {
    @copy(__DIR__ . '/../bootstrap/cache/services.php', $bootstrapCache . '/services.php');
}

putenv("APP_SERVICES_CACHE={$bootstrapCache}/services.php");
putenv("APP_PACKAGES_CACHE={$bootstrapCache}/packages.php");
putenv("APP_CONFIG_CACHE={$bootstrapCache}/config.php");
putenv("APP_ROUTES_CACHE={$bootstrapCache}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$bootstrapCache}/events.php");

$_ENV['APP_SERVICES_CACHE'] = "{$bootstrapCache}/services.php";
$_ENV['APP_PACKAGES_CACHE'] = "{$bootstrapCache}/packages.php";
$_ENV['APP_CONFIG_CACHE'] = "{$bootstrapCache}/config.php";
$_ENV['APP_ROUTES_CACHE'] = "{$bootstrapCache}/routes-v7.php";
$_ENV['APP_EVENTS_CACHE'] = "{$bootstrapCache}/events.php";

// Prepare writable storage directory in /tmp
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/app/public',
    $storagePath . '/app/private',
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

putenv("APP_STORAGE={$storagePath}");
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
$_ENV['APP_STORAGE'] = $storagePath;
$_SERVER['APP_STORAGE'] = $storagePath;

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Application Exception</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (line " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
