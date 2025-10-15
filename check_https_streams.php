<?php
$playlists = [
    'everything.m3u',
    'personal-channels.m3u'
];

function testStream($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true); // only headers
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code >= 200 && $code < 400;
}

$results = [];

foreach ($playlists as $file) {
    $fullpath = __DIR__ . '/' . $file;
    if (!file_exists($fullpath)) continue;

    $playlist = file($fullpath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $found = [];

    for ($i = 0; $i < count($playlist); $i++) {
        $line = trim($playlist[$i]);

        if (stripos($line, '#EXTINF') === 0 && isset($playlist[$i + 1])) {
            $name = trim(substr($line, strpos($line, ',') + 1));
            $url = trim($playlist[$i + 1]);

            if (stripos($url, 'http://') === 0) {
                $https_url = preg_replace('/^http:/i', 'https:', $url);
                $status = testStream($https_url) ? "✅ WORKS" : "❌ FAIL";

                $found[] = [
                    'name' => $name,
                    'http' => $url,
                    'https' => $https_url,
                    'status' => $status
                ];
            }
        }
    }

    $results[$file] = $found;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>HTTPS Stream Checker</title>
    <style>
        body { background: #111; color: #eee; font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #00ffcc; }
        h2 { color: #cccccc; border-bottom: 1px solid #444; padding-bottom: 5px; margin-top: 40px; }
        .ok { color: #00ff00; }
        .fail { color: #ff4444; }
        .block { margin-bottom: 10px; }
        a { color: #00aaff; }
    </style>
</head>
<body>
    <h1>🛰️ HTTPS Compatibility Report</h1>

    <?php foreach ($results as $playlistName => $streams): ?>
        <h2>📂 <?= htmlspecialchars($playlistName) ?></h2>
        <?php if (empty($streams)): ?>
            <p>No HTTP streams found.</p>
        <?php else: ?>
            <ul>
            <?php foreach ($streams as $stream): ?>
                <li class="block">
                    <?= htmlspecialchars($stream['name']) ?>:<br>
                    <?php if ($stream['status'] === '✅ WORKS'): ?>
                        <span class="ok">✔ HTTPS works:</span>
                        <a href="<?= $stream['https'] ?>" target="_blank"><?= $stream['https'] ?></a>
                    <?php else: ?>
                        <span class="fail">✖ HTTPS failed:</span>
                        <a href="<?= $stream['http'] ?>" target="_blank"><?= $stream['http'] ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endforeach; ?>
</body>
</html>
