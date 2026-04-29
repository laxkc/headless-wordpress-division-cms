<?php

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$documentRoot = '/var/www/html';

if (str_starts_with($uri, '/index.php/')) {
    $target = substr($uri, strlen('/index.php'));
    $query = $_SERVER['QUERY_STRING'] ?? '';
    if ($query !== '') {
        $target .= '?' . $query;
    }

    header('Location: ' . $target, true, 302);
    exit;
}

$requested = realpath($documentRoot . $uri);
if ($requested !== false && str_starts_with($requested, $documentRoot) && is_file($requested)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $documentRoot . '/index.php';

require $documentRoot . '/index.php';
