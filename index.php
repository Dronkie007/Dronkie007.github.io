<?php
$playlist_path = __DIR__ . '/everything.m3u';

if (!file_exists($playlist_path)) {
    echo "Playlist not found.";
    exit;
}

$lines = file($playlist_path);


for ($i = 0; $i < count($lines); $i++) {
    $line = trim($lines[$i]);

    if (str_starts_with($line, '#EXTINF')) {
        // Extract channel name
        $name = trim(substr($line, strpos($line, ',') + 1));

        // Skip any following lines like #EXTVLCOPT
        $j = $i + 1;
        while ($j < count($lines) && str_starts_with(trim($lines[$j]), '#')) {
            $j++;
        }

        // Only add if there's a stream URL following
        if ($j < count($lines)) {
            $channels[] = $name;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/iptv/images/Favicons/favicon.ico" type="image/x-icon">
   <!-- <title>Daar is alweer niks op TV</title> -->
</head>
<style>
    body {
        background-color: #121212;
        color: #f0f0f0;
        font-family: Arial, sans-serif;
        padding: 20px;
		text-align: center;
    }

    h1 {
        color: #ffff00;
		text-align: center;
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
    }
	.txt1 {
		font-size: 5em;
		font-weight: bold;
	}
	
	.txt2 {
		font-size: 3em;
		font-weight: bold;
	}
    .txt3 {
        font-size: 1.5em;
        font-weight: bold;
        color: #280A3E;
    }
	.txt4 {
	    font-size: 5em;
	    font-wight: bold;
	    color: red
	}

    .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 10px;
}

.card {
    background-color: #222;
    border-radius: 10px;
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
    color: #FFD700;
    margin-bottom: 5px;
}

.buttons a.btn {
    display: inline-block;
    margin: 5px;
    padding: 4px 6px;
    border-radius: 1px;
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
    padding: 8px 12px;
    background: #4E1F00;
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
}

#filters button.active {
    background: #00aaff;
}

.hero {
    background: url('/iptv/images/background3.png') no-repeat center center;
    background-size: cover;
    height: 600px; /* adjust as needed */
    position: relative;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.hero-text {
    background: rgba(0, 0, 0, 0.0);
    padding: 50px;  /* used for text overlay */
    border-radius: 20px;
    color: white;
}



</style>

<body style="background-color: black; color: FFD700;">

	<header>
        <div class='hero'>
	    <div text-align: left; class='hero-text'>
            <h1 class='txt2' style='color:black'><u>The links are most working, if live does not work, download link and play in VLC and will play the link.</u></h1>
            <h1 class='txt1'>Internet TV</h1>
            <p class='txt3' style='color:white'>Download link channel in another player of your choice. eg. <a target='_blank' href='https://www.videolan.org/vlc/'>VLC</a></p>
	        <p class='txt3'; style='color:white'>Send suggestions to: <a href='mailto:info@007tech.co.za?subject=Suggestion'>Email</a></p>
	    </div>
	    </div>
    </header>

	<div>
    <input type="text" id="search" placeholder="Search for a Channel..." />
    <div id="filters"></div>
</div>

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
        // echo "<a href='generate_m3u.php?name=" . urlencode($name) . "' class='btn'><img src=images/download_icon.jpg width='100' height='40'></a>";
        echo "<a href='/iptv/embedplayer.html?url=" . urlencode($url) . "' class='btn' target='_blank'><img src=images/streaming_icon.jpg width='100' height='40'></a>";
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


</body>

<footer>
	<div>
	    <a href='/iptv/m3u/personal/index_personal.php' class='txt3'>Ӎ¡ҪḩǼԼ</a>
        <br>
        <a href='/login_system/main.html' style="color: red;"><h6>Games Download</h6></a>
	</div>
</footer>

</html>
