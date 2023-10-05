<?php
require_once 'assets/php/utils.php';
require_once 'assets/php/html_strings/nav_bar.php';
require_once 'assets/php/html_strings/explore_content.php';
require_once 'assets/php/sql_strings/retrieve_videos.php';
require_once 'assets/php/sql_strings/retrive_video_tags.php';
require_once 'assets/php/sql_strings/retrieve_video_views.php';
require_once 'assets/php/connections/pdo.php';

// session_start();

$nav = setActiveNav('explore', $nav);

// retrive all videos from database
$stmt = $conn->prepare($videos_sql);
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create explore content
$explore_content = '';

if (count($videos) == 0) {
    $explore_content = '<h1 class="text-center">No videos found</h1>';
} else {
    $explore_content .= $explore_row_start_top;
}

// Loop through all videos 
$counter = 0;
foreach ($videos as $video) {
    // retrive video tags
    $stmt = $conn->prepare($video_tags_sql);
    $stmt->execute(array(':video_id' => $video['id']));
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create tags string
    $tags_string = '';
    foreach ($tags as $tag) {
        $tags_string .= $tag['tag'] . '--';
    }
    $tags_string = rtrim($tags_string, '--');

    if ($counter != 0 && $counter % 4 == 0) {

        $explore_content .= $explore_row_end;
        
        $explore_content .= $explore_row_start;

    }

    // retrieve views
    $stmt = $conn->prepare($video_views_sql);
    $stmt->execute(array(':vid' => $video['id']));
    $views = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Create explore content
    
    $explore_content .= str_replace(':vid', $video['id'], $explore_row_inner);
    $explore_content = str_replace(':thumbnail', $video['thumbnail'], $explore_content);
    $explore_content = str_replace(':tags', $tags_string, $explore_content);
    $explore_content = str_replace(':views', $views['view_count'], $explore_content);
    $explore_content = str_replace(':title', $video['vid_name'], $explore_content);

    $counter++;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Gallery</title>
    <!-- Add Bootstrap CSS link here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Add FontAwesome CSS link here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Add custom CSS for styling -->
    <style>
        /* Add custom CSS styles here */
        .video-thumbnail {
            position: relative;
            margin-bottom: 20px;
            overflow: hidden; /* Ensure uniform thumbnail size */
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
        .card-body {
            background-color: rgba(0, 0, 0, 0.1); /* Slight background for card body */
            text-align: center; /* Center-align card content */
            position: relative; /* Make the button positioning relative to the card body */
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
        .play-button {
            font-size: 24px;
            color: #fff;
        }
        .row-divider {
            border-top: 1px solid #ddd;
            margin-top: 20px; /* Add spacing between rows */
            padding-top: 20px; /* Add space between row borders and row content */
        }
        .user-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            line-height: 40px;
        }
    </style>
</head>
<body>
    <?php echo $nav; ?>
    <div class="container">
        <?php echo $explore_content; ?>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/
</body>
</html>