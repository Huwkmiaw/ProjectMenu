<?php

// Prepare storage directory in /tmp for Vercel serverless environment
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/app/public',
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
$_ENV['APP_STORAGE'] = $storagePath;
$_SERVER['APP_STORAGE'] = $storagePath;

require __DIR__ . '/../public/index.php';
