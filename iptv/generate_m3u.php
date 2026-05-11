<?php
$allowed_referrer = 'https://007tech.co.za';

if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $allowed_referrer) === false) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied.');
}

$name = $_GET['name'] ?? '';
$playlist = file(__DIR__ . '/everything.m3u');

$output = [];
for ($i = 0; $i < count($playlist); $i++) {
    if (str_starts_with(trim($playlist[$i]), '#EXTINF')) {
        $line_name = trim(substr($playlist[$i], strpos($playlist[$i], ',') + 1));
        if (strcasecmp($name, $line_name) === 0) {
            $output[] = "#EXTM3U";
            $output[] = trim($playlist[$i]);

            // Include optional #EXTVLCOPT lines
            $j = $i + 1;
            while ($j < count($playlist) && str_starts_with(trim($playlist[$j]), '#')) {
                $output[] = trim($playlist[$j]);
                $j++;
            }

            // Add the stream link
            if ($j < count($playlist)) {
                $output[] = trim($playlist[$j]);
            }

            break;
        }
    }
}

if (empty($output)) {
    header("HTTP/1.1 404 Not Found");
    echo "Channel not found.";
    exit;
}

header("Content-Type: audio/x-mpegurl");
header("Content-Disposition: attachment; filename=\"" . str_replace(' ', '_', $name) . ".m3u\"");
echo implode("\n", $output);
