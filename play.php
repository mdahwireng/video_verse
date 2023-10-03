<?php
require_once "assets/php/utils.php";
require_once "assets/php/html_strings/nav_bar.php";
require_once "assets/php/html_strings/explore_content.php";
require_once "assets/php/html_strings/play_content.php";
require_once "assets/php/sql_strings/retrieve_videos.php";
require_once "assets/php/sql_strings/retrive_video_tags.php";
require_once "assets/php/sql_strings/retrieve_video_views.php";
require_once "assets/php/sql_strings/retrive_videos_with_tag.php";
require_once "assets/php/connections/pdo.php";
require_once "assets/php/sql_strings/retrive_video_with_id.php";

$vid_for_player = $_GET['vid'];
$recommended_tags = $_GET['tags'];

$nav = setActiveNav("explore", $nav);

// retrive video for player
$stmt = $conn->prepare($videos_id_sql);
$stmt->execute(array(":vid" => $vid_for_player));
$video = $stmt->fetch(PDO::FETCH_ASSOC);

// format player html
$video_player = str_replace(":vpath", $video["vid_path"], $video_player);
$video_player = str_replace(":title", $video["vid_name"], $video_player);
$video_player = str_replace(":vid", $vid_for_player, $video_player);


// split tags
$tags = explode("--", $recommended_tags);

// create tag where clause
$tag_where_clause = "";
foreach ($tags as $tag) {
    $tag_where_clause .= " OR t.tag ='" . $tag . "'";
}
$tag_where_clause = ltrim($tag_where_clause, " OR");
// print_r($tag_where_clause);
// return;
// retrive recommended videos

$videos_with_tags_sql = str_replace(":tag_where_clause", $tag_where_clause, $videos_with_tags_sql);

$stmt = $conn->prepare($videos_with_tags_sql);
$stmt->execute();

$recommended_videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($recommended_videos);
// return;

// create recommended videos html
$recommended_videos_html = "";

foreach ($recommended_videos as $recommended_video) {
    // retrieve views
    $stmt = $conn->prepare($video_views_sql);
    $stmt->execute(array(":vid" => $recommended_video["id"]));
    $views = $stmt->fetch(PDO::FETCH_ASSOC);

    // retrive video tags
    $stmt = $conn->prepare($video_tags_sql);
    $stmt->execute(array(":video_id" => $recommended_video["id"]));
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create tags string
    $tags_string = "";
    foreach ($tags as $tag) {
        $tags_string .= $tag["tag"] . "--";
    }
    $tags_string = rtrim($tags_string, "--");



    // Create explore content

    $recommended_videos_html .= str_replace(":vid", $recommended_video["id"], $recommended_vid);
    $recommended_videos_html = str_replace(":thumbnail", $recommended_video["thumbnail"], $recommended_videos_html);
    $recommended_videos_html = str_replace(":tags", $tags_string, $recommended_videos_html);
    $recommended_videos_html = str_replace(":views", $views["view_count"], $recommended_videos_html);
    $recommended_videos_html = str_replace(":title", $recommended_video["vid_name"], $recommended_videos_html);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Player</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add FontAwesome CSS link here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Add custom CSS for styling -->
    <style>
        /* Add custom CSS styles here */
        .video-container {
            height: 100vh; /* Fixed height to the viewport */
            display: flex;
        }
        .video-player {
            flex: 2; /* 8 columns for the video player */
            padding: 20px;
        }
        .recommended-videos {
            flex: 0.5; /* 4 columns for recommended videos */
            padding: 20px;
            overflow-y: scroll; /* Enable scrolling for recommended videos */
        }
        .video-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            background-color: #f0f0f0;
        }
        .thumbnail-image {
            max-width: 100%;
            height: auto;
        }
        .views-label {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #007bff; /* Secondary color for the label */
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .video-description {
            padding: 10px;
        }
        .video-player-frame {
            width: 100%;
            height: 500px; /* Adjust the height as needed */
        }
        .play-button-container {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            z-index: 1; /* Ensure play button appears above card content */
        }
    </style>
</head>
<body>
    <?php echo $nav; ?>

    <div class="container-fluid video-container mt-5">
        
        <div class="video-player">
            <?php echo $video_player; ?>
        </div>
        <div class="recommended-videos">
            <?php echo $recommended_videos_html; ?>
        </div>
    </div>

    <!-- Add Bootstrap JS and jQuery scripts here -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // JavaScript code to track video views
    // const video = document.getElementById('videoElement'); // Get the video element

    // video.addEventListener('play', () => {
    //     console.log('Video started playing');
    //     console.log(video.currentTime); // Get the current time of the video
    // });

    // console.log(video); // Get the current time of the video


    // Get the video element by ID
const videoElement = document.getElementById('videoElement');

// Add an event listener for the 'play' event
videoElement.addEventListener('play', function() {
    // Check if the video has not been played in this view
    const hasNotBeenPlayed = videoElement.getAttribute('data-played') === 'false';

    if (hasNotBeenPlayed) {
        // Get the video ID (replace with your logic to retrieve the video ID)
        const videoId = videoElement.getAttribute('vid');

        // Send an AJAX request to record the play event
        recordPlayEvent(videoId);

        // Set the 'data-played' attribute to 'true' to prevent multiple plays from being recorded
        videoElement.setAttribute('data-played', 'true');
    }
});

// Function to send an AJAX request to record the play event
function recordPlayEvent(videoId) {
    // Create an XMLHttpRequest object
    const xhr = new XMLHttpRequest();

    // Define the URL of the PHP script that records the play event
    const url = 'views.php';

    // Define the data to send in the AJAX request, video id and datetime video started playing
    var playTime = new Date();


    const data = `videoId=${videoId}&start=${new Date().getTime()}`;

    console.log(data);

    // Configure the AJAX request
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Handle the response (optional)
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle the response from the server (if needed)
            console.log(xhr.responseText);
        }
    };

    // Send the AJAX request
    xhr.send(data);
}


    </script>
</body>
</html>
