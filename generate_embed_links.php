<?php
$playlist = file(__DIR__ . '/personal-channels.m3u', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$links = [];

for ($i = 0; $i < count($playlist); $i++) {
    $line = trim($playlist[$i]);

    if (stripos($line, '#EXTINF') === 0) {
        $name = trim(substr($line, strpos($line, ',') + 1));
        
        // Get next non-comment line (stream URL)
        $j = $i + 1;
        while ($j < count($playlist) && $playlist[$j][0] === '#') {
            $j++;
        }

        if ($j < count($playlist)) {
            $url = trim($playlist[$j]);
            $encodedUrl = urlencode($url);
            $escapedName = htmlspecialchars($name);
            $links[] = "<a href='embedplayer.html?url=$encodedUrl' target='_blank'>▶ $escapedName</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Personal Preference</title>
    <style>
        body { background: #111; color: #eee; font-family: Arial; padding: 20px; }
        a { color: #00ff99; text-decoration: none; display: block; margin-bottom: 10px; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>📺 TV</h1>
    <?php foreach ($links as $link): echo $link, "\n"; endforeach; ?>
</body>
</html>
