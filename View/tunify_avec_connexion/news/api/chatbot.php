<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Adjust for production

$geniusApiKey = 'YWRF4Bp6gqCJ9hbUvCDd1uJMzQs9QZncWl-97fvh4zVOifqExuadHoA5Hv9F-3eC';
$youtubeApiKey = 'AIzaSyBrK04ws2I1YXJsu2UGBjFeISgD0weKEk4';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$query = trim($input['message'] ?? '');

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

function getYoutubeChannel($artist, $youtubeApiKey) {
    $query = urlencode($artist);
    $searchUrl = "https://www.googleapis.com/youtube/v3/search?part=snippet&q={$query}&type=channel&maxResults=5&key={$youtubeApiKey}";
    $searchResponse = json_decode(file_get_contents($searchUrl), true);

    if (empty($searchResponse['items'])) {
        return null;
    }

    // Loop through up to 5 channels to find one that matches 'music' topic
    foreach ($searchResponse['items'] as $item) {
        $channelId = $item['id']['channelId'];
        $detailsUrl = "https://www.googleapis.com/youtube/v3/channels?part=statistics,topicDetails,snippet&id={$channelId}&key={$youtubeApiKey}";
        $detailsResponse = json_decode(file_get_contents($detailsUrl), true);

        if (empty($detailsResponse['items'][0])) {
            continue;
        }

        $channel = $detailsResponse['items'][0];

        // Check if topicCategories exist and contain music
        if (!empty($channel['topicDetails']['topicCategories'])) {
            foreach ($channel['topicDetails']['topicCategories'] as $categoryUrl) {
                if (stripos($categoryUrl, 'music') !== false) {
                    return [
                        'channelTitle' => $channel['snippet']['title'] ?? 'Unknown channel',
                        'channelId' => $channelId,
                        'thumbnail' => $channel['snippet']['thumbnails']['default']['url'] ?? '',
                        'subscriberCount' => $channel['statistics']['subscriberCount'] ?? '0',
                        'channelUrl' => "https://www.youtube.com/channel/" . $channelId
                    ];
                }
            }
        }
    }

    // If no music-related channel found, return null
    return null;
}


function getGeniusData($query, $geniusApiKey) {
    $url = "https://api.genius.com/search?q=" . urlencode($query);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $geniusApiKey"
    ]);

    $output = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($output, true);
    $firstHit = $data['response']['hits'][0]['result'] ?? null;

    if ($firstHit) {
        return [
            'title' => $firstHit['title'] ?? 'Unknown title',
            'primary_artist' => ['name' => $firstHit['primary_artist']['name'] ?? 'Unknown artist'],
            'url' => $firstHit['url'] ?? '',
            'release_date' => $firstHit['release_date'] ?? null,
            'lyrics_state' => $firstHit['lyrics_state'] ?? 'unavailable',
        ];
    }

    return null;
}

function getYoutubeVideo($query, $youtubeApiKey) {
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($query) . "&type=video&maxResults=1&key=$youtubeApiKey";
    $data = json_decode(file_get_contents($url), true);

    if (!empty($data['items'][0])) {
        $videoId = $data['items'][0]['id']['videoId'];
        $videoTitle = $data['items'][0]['snippet']['title'];
        $publishedAt = $data['items'][0]['snippet']['publishedAt'];
        $url = "https://www.youtube.com/watch?v=" . $videoId;

        return [
            'videoId' => $videoId,
            'title' => $videoTitle,
            'publishedAt' => $publishedAt,
            'url' => $url
        ];
    }

    return null;
}

// Fetch artist info (YouTube channel)
$youtubeChannel = getYoutubeChannel($query, $youtubeApiKey);

// Fetch song info (Genius + YouTube video)
$geniusData = getGeniusData($query, $geniusApiKey);
$songData = null;

if ($geniusData) {
    $title = $geniusData['title'] ?? 'Unknown title';
    $artist = $geniusData['primary_artist']['name'] ?? 'Unknown artist';
    $geniusUrl = $geniusData['url'] ?? '';
    $lyricsAvailable = $geniusData['lyrics_state'] ?? 'unavailable';

    $youtubeVideo = getYoutubeVideo("$artist $title", $youtubeApiKey);

    // Improved release_date handling
    $releaseDate = $geniusData['release_date'] ?? null;
    if (empty($releaseDate)) {
        $releaseDate = $youtubeVideo['publishedAt'] ?? null;
        if ($releaseDate) {
            $releaseDate = date('F j, Y', strtotime($releaseDate));
        } else {
            $releaseDate = "Unknown release date";
        }
    }

    $videoTitle = $youtubeVideo['title'] ?? 'N/A';
    $publishedAt = isset($youtubeVideo['publishedAt']) ? date('F j, Y', strtotime($youtubeVideo['publishedAt'])) : 'N/A';

    // Get view count (optional extra request)
    $viewCount = 'N/A';
    if (!empty($youtubeVideo['videoId'])) {
        $videoId = $youtubeVideo['videoId'];
        $videoDetailsUrl = "https://www.googleapis.com/youtube/v3/videos?part=statistics&id={$videoId}&key={$youtubeApiKey}";
        $videoDetails = json_decode(file_get_contents($videoDetailsUrl), true);
        $viewCount = $videoDetails['items'][0]['statistics']['viewCount'] ?? 'N/A';
        if (is_numeric($viewCount)) {
            $viewCount = number_format($viewCount);
        }
    }

    $videoUrl = $youtubeVideo['url'] ?? '#';

    $songData = [
        'title' => $title,
        'artist' => $artist,
        'release_date' => $releaseDate,
        'youtube_published' => $publishedAt,
        'views' => $viewCount,
        'genius_url' => $geniusUrl,
        'youtube_url' => $videoUrl,
        'lyrics_available' => $lyricsAvailable !== 'unavailable',
    ];
}

// If neither found
if (!$youtubeChannel && !$songData) {
    echo json_encode([
        'success' => false,
        'message' => "No artist or song found for '{$query}'."
    ]);
    exit;
}

// Return both results
echo json_encode([
    'success' => true,
    'artist' => $youtubeChannel ? [
        'channel_name' => $youtubeChannel['channelTitle'] ?? 'Unknown channel',
        'subscribers' => number_format($youtubeChannel['subscriberCount'] ?? 0),
        'channel_url' => $youtubeChannel['channelUrl'] ?? null,
    ] : null,
    'song' => $songData,
]);
