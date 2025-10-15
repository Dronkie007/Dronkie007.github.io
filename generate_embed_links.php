<?php
$name = $_GET['name'] ?? null;
$playlist_path = __DIR__ . '/everything.m3u'; // or whichever file you're using

if (!file_exists($playlist_path)) {
    die("Playlist not found.");
}

$lines = file($playlist_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$found = false;

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Stream: " . htmlspecialchars($name) . "</title></head><body style='background-color: black; color: white;'>";

for ($i = 0; $i < count($lines); $i++) {
    if (stripos($lines[$i], '#EXTINF') === 0 && isset($lines[$i + 1])) {
        $line = $lines[$i];
        $url = $lines[$i + 1];

        $line_name = trim(substr($line, strpos($line, ',') + 1));

        if (strcasecmp($name, $line_name) === 0) {
            echo "<h1>" . htmlspecialchars($line_name) . "</h1>";
            echo "<video controls autoplay style='width:100%; max-width:800px;'>";
            echo "<source src='" . htmlspecialchars($url) . "' type='application/x-mpegURL'>";
            echo "Your browser does not support embedded video.";
            echo "</video>";
            $found = true;
            break;
        }

        $i++; // skip to next pair
    }
}

if (!$found) {
    echo "<p>❌ Stream not found: " . htmlspecialchars($name) . "</p>";
}

echo "</body></html>";
?>


<!DOCTYPE html>
<html>
<head>
    <title>Generated TV Links</title>
    <style>
        body { background: #111; color: #eee; font-family: Arial; padding: 20px; }
        a { color: #80ff00 text-decoration: none; display: block; margin-bottom: 10px; font-size: 1.5em; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>📺 IPTV</h1>
    <?php foreach ($links as $link): echo $link, "\n"; endforeach; ?>
</body>
</html>
