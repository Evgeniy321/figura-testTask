<?php

if (!function_exists('myDebugLog')) {
    function myDebugLog($data, $label = '')
    {
        $logPath = $_SERVER['DOCUMENT_ROOT'] . '/local/debug.log';
        $message = date('Y-m-d H:i:s') . ' | ' . $label . ' | ' . print_r($data, true) . "\n";
        file_put_contents($logPath, $message, FILE_APPEND);
    }
}