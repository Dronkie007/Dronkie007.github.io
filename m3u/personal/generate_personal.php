<?php
$allowed_referrer = 'https://007tech.co.za'; // your domain

if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $allowed_referrer) === false) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied.');
}

$name = $_GET['name'] ?? '';
$playlist = file(__DIR__ . '/personal-channels.m3u', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$output = [];
for ($i = 0; $i < count($playlist); $i++) {
    if (strpos($playlist[$i], '#EXTINF') === 0) {
        $line_name = trim(substr($playlist[$i], strpos($playlist[$i], ',') + 1));
        if (strcasecmp($name, $line_name) === 0) {
            $output[] = "#EXTM3U";
            $output[] = trim($playlist[$i]);
            $j = $i + 1;
            while ($j < count($playlist) && !str_starts_with(trim($playlist[$j]), '#EXTINF')) {
                $output[] = trim($playlist[$j]);
                $j++;
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
header("Cache-Control: no-cache");
echo implode("\n", $output);
exit;
?>
