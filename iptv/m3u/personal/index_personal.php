<?php
$playlist_path = __DIR__ . '/personal-channels.m3u';

if (!file_exists($playlist_path)) {
    echo "Playlist not found.";
    exit;
}

$lines = file($playlist_path);
$channels = [];

for ($i = 0; $i < count($lines); $i++) {
    $line = trim($lines[$i]);

    if (str_starts_with($line, '#EXTINF')) {
        $name = trim(substr($line, strpos($line, ',') + 1));

        // Skip optional lines
        $j = $i + 1;
        while ($j < count($lines) && str_starts_with(trim($lines[$j]), '#')) {
            $j++;
        }

        if ($j < count($lines)) {
            $channels[] = $name;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/iptv/images/Favicons/favicon.ico" type="image/x-icon">
    <title>Channels</title>
</head>

<style>
    body {
        background-color: #17313E;
        color: #f0f0f0;
        font-family: Arial, sans-serif;
        padding: 20px;
		text-align: center
    }

    h1 {
        color: #ffffff;
    }

    a {
        color: #1e90ff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    li {
        margin: 10px 0;
		color: 	#00ffff
    }

	.txt1 {
		  font-size: 5em;
		  font-weight: bold;
	}

	.txt2 {
		  font-size: 3em;
		  font-weight: bold;
		  color: #42a5f5;
	}

	.txt3 {
		  font-size: 1.5em;
		  font-weight: bold;
	}

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        padding: 10px;
    }

    .card {
        background-color: #222;
        border-radius: 40px;
        padding: 5px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.5);
        transition: transform 0.2s;
    }

    .card:hover {
        transform: scale(1.02);
    }

    .title {
        font-size: 1.2em;
        font-weight: bold;
        color: #8DD8FF;
        margin-bottom: 5px;
    }

    .buttons a.btn {
        display: inline-block;
        margin: 5px;
        padding: 5px 10px;
        border-radius: 40px;
        background: #000000;
        color: white;
        text-decoration: none;
        font-size: 0.9em;
        transition: background 0.2s;
    }

    .buttons a.btn:hover {
        background: #7A85C1;
    }


    #search {
        width: 100%;
        padding: 20px;
        font-size: 1.5em;
        margin-bottom: 20px;
        margin-top: 20px;
        background: #222;
        border: none;
        border-radius: 30px;
        color: #4DFFBE;
    }

    #filters {
        margin-bottom: 20px;
    }

    #filters button {
        margin: 5px;
        padding: 4px 6px;
        background: #780000;
        border: none;
        border-radius: 5px;
        color: #fcf300;
        cursor: pointer;
    }

    #filters button.active {
        background: #00aaff;
    }

    .hero {
        background: url('/iptv/images/Thank you for watching.jpg') no-repeat;  /* should be: no-repeat center center; vir background center */
        background-size: contain;
        height: auto; /* adjust as needed */
        min-height: 200px;
        width: 100%;
        position: relative;
        color: black;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #000;
    }

    .hero-text {
        background: rgba(0, 0, 0, 0.0);
        padding: 110px;
        border-radius: 20px;
        color: white;
        top: 1px;
        position: absolute;
    }


</style>

<body style="background-color: #09122C; color: FFD700;">

<body>
    <header>
	    <div class='hero'>
            <div text-align: left; class='hero-text'>
                <a class='txt2' href="generate_embed_links.php">Kanale lys</a></div>
            </div>
        </div>
    </header>
<div>
    <div id='filters'></div>

    <div class="grid" id="channelGrid">

<?php
$lines = file($playlist_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$channels = [];
$categories = [];

for ($i = 0; $i < count($lines); $i++) {
    if (stripos($lines[$i], '#EXTINF') === 0 && isset($lines[$i + 1])) {
        $line = $lines[$i];
        $url = $lines[$i + 1];

        // Extract name
        $name = trim(substr($line, strpos($line, ',') + 1));

        // Extract category
        preg_match('/group-title="([^"]+)"/', $line, $match);
        $category = $match[1] ?? 'Uncategorized';

        $categories[$category] = true;

        echo "<div class='card' data-name='" . htmlspecialchars($name) . "' data-category='" . htmlspecialchars($category) . "'>";
        echo "<div class='title'>" . htmlspecialchars($name) . "</div>";
        echo "<div class='buttons'>";
        echo "<a href='generate_m3u.php?name=" . urlencode($name) . "' class='btn'><img src=/iptv/images/VLCIcon.jpeg width='50' height='40'> Play in VLC</a>";
        // echo "<a href='generate_m3u.php?name=" . urlencode($name) . "' class='btn'><img src=/iptv/images/download_icon_download.jpg width='100' height='40'></a>";
        echo "<a href='embedplayer.html?url=" . urlencode($url) . "' class='btn' target='_blank'><img src=/iptv/images/streaming_icon.jpg width='90' height='40'></a>";
        echo "</div></div>";

        $i++; // skip to next pair
    }
}
?>
</div>

<script>
const cards = document.querySelectorAll('.card');
const search = document.getElementById('search');
const filters = document.getElementById('filters');

// Extract categories from cards
let categories = new Set();
cards.forEach(card => {
    categories.add(card.dataset.category);
});

// Create filter buttons
categories.forEach(cat => {
    const btn = document.createElement('button');
    btn.textContent = cat;
    btn.addEventListener('click', () => {
        document.querySelectorAll('#filters button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        filterCards(search.value, cat);
    });
    filters.appendChild(btn);
});

search.addEventListener('input', () => {
    const activeBtn = document.querySelector('#filters .active');
    const cat = activeBtn ? activeBtn.textContent : '';
    filterCards(search.value, cat);
});

function filterCards(term, category) {
    term = term.toLowerCase();
    cards.forEach(card => {
        const name = card.dataset.name.toLowerCase();
        const cat = card.dataset.category;
        const matchesText = name.includes(term);
        const matchesCat = !category || cat === category;
        card.style.display = matchesText && matchesCat ? '' : 'none';
    });
}
</script>

<p class='txt3' style='color: #90caf9;'>Kanaal het baie ads. Waneer games geload is en werk, maak die extra tabs toe.</p>
<a target='_blank' class='txt2' href='https://www.fctv33.work/' style='color: #90caf9;'><h5>Sports</h5></a>
<a class='txt2' href='/'>Home</a>
<br>
<footer>
<h1>hierdie sal github site biekie run</h1>
</footer>


</body>

</html>
