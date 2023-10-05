<?php

require_once "assets/php/connections/pdo.php";
require_once "assets/php/html_strings/nav_bar.php";
require_once "assets/php/sql_strings/retrieve_video_views_agregated.php";
require_once "assets/php/sql_strings/retrive_video_tags.php";
require_once "assets/php/utils.php";



if (!isset($_SESSION['name'])) {
    header('Location: index.php');
    return;
}

$nav = setActiveNav('dashboard', $nav);


$summaries_html =  '<div class="row">
                    <div class="col-md-4" id="owned-videos-summary">
                        <div class="summary-card">
                            <h2>Videos Owned</h2>
                            <p class="d-flex justify-content-between align-items-center">
                                <span>:num_owned videos</span>
                                <span class="float-right">Views: <span id="owned-views">:owned_views</span></span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4" id="uploaded-videos-summary">
                        <div class="summary-card">
                            <h2>Videos Uploaded</h2>
                            <p class="d-flex justify-content-between align-items-center">
                                <span>:num_uploaded videos</span>
                                <span class="float-right">Views: <span id="uploaded-views">:uploaded_views</span></span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4" id="total-views-summary">
                        <div class="summary-card">
                            <h2>Total Views</h2>
                            <p>:tot_views views</p>
                        </div>
                    </div>
                    </div>';


$owned_by_html = "";
$uploaded_by_html = "";


$fill_in_html = '<div class="col-md-3">
                    <div class="card video-thumbnail">
                        
                        <div class="play-button-container">
                            <a href="play.php?vid=:vid&tags=:tags"><i class="fas fa-play play-button"></i></a>
                        </div>
                        
                        <img src=":thumbnail" alt="Video Thumbnail" class="card-img-top thumbnail-image">
                        <span class="views-label">:views Views</span>
                        
                        <div class="card-body">
                            <h5 class="card-title">:title</h5>
                        </div>
                    </div>
                </div>';

        
$user_id = $_SESSION['user_id'];


// retrive videos uploaded or owned by user
$stmt = $conn->prepare($retrive_view_ags_sql);
$stmt->execute(array(
    ':uid' => $user_id
));

$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$owned_videos = 0;
$owned_views = 0;
$uploaded_videos = 0;
$uploaded_views = 0;
$total_views = 0;

foreach ($videos as $video) {
    if ($video['owned_by'] == $user_id) {

        $hld = str_replace(':vid', $video['id'], $fill_in_html);
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

        $hld = str_replace(':tags', $tags_string, $hld);
        $hld = str_replace(':thumbnail', $video['thumbnail'], $hld);
        $hld = str_replace(':views', $video['vid_views'], $hld);
        $hld = str_replace(':title', $video['vid_name'], $hld);
        
        $owned_by_html .= $hld;

        $owned_videos++;
        $owned_views += $video['vid_views'];
    } 
    
    if ($video['uploaded_by'] == $user_id){
        $hld = str_replace(':vid', $video['id'], $fill_in_html);
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

        $hld = str_replace(':tags', $tags_string, $hld);
        $hld = str_replace(':thumbnail', $video['thumbnail'], $hld);
        $hld = str_replace(':views', $video['vid_views'], $hld);
        $hld = str_replace(':title', $video['vid_name'], $hld);

        $uploaded_by_html .= $hld;

        $uploaded_videos++;
        $uploaded_views += $video['vid_views'];
    }

    $total_views += $video['vid_views'];


}


$summaries_html = str_replace(':num_owned', $owned_videos, $summaries_html);
$summaries_html = str_replace(':num_uploaded', $uploaded_videos, $summaries_html);
$summaries_html = str_replace(':owned_views', $owned_views, $summaries_html);
$summaries_html = str_replace(':uploaded_views', $uploaded_views, $summaries_html);
$summaries_html = str_replace(':tot_views', $total_views, $summaries_html);



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Hub Dashboard</title>
    <!-- Bootstrap CSS link (add your own CDN link or local file) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Custom CSS for the dashboard */
        /* body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #007BFF;
            color: #fff;
        }
        .container-fluid {
            padding: 20px;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
        } */

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
        .summary-card {
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer; /* Make cards clickable */
        }
        .video-list {
            list-style-type: none;
            padding: 0;
        }
        .video-item {
            /* border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fff; */
            display: none; /* Hide video items by default */
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

    <!-- Dashboard Content -->
    <div class="container mt-2 mb-2">
        <h1>Welcome to Your VideoVerse Dashboard</h1>

        <!-- Top Section with Summaries -->
        <?php echo $summaries_html; ?>


        <!-- Video Display Section -->
        <div class="row-divider"></div>
            <!-- Videos Owned -->
            <!-- <div class="owned-videos video-item row align-items-center"> -->
            <div class="row owned-videos align-items-center" style="display:none;">
                <div class="col-12 mt-5">
                    <h2>Videos Owned</h2>
                </div>
                <?php echo $owned_by_html; ?>
            </div>
                
            <!-- </div> -->
            
            <!-- Videos Uploaded -->
            <div class="row row-divder uploaded-videos align-items-center" style="display:none;">
                <div class="col-12 mt-5">
                    <h2>Videos Uploaded</h2>
                </div>
                <?php echo $uploaded_by_html; ?>
            </div>
            
    </div>
    </div>

    <!-- Bootstrap JS scripts (add your own CDN links or local files) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        // Toggle the visibility of video items when a summary card is clicked
        $(document).ready(function() {
            $("#owned-videos-summary").click(function() {
                $(".owned-videos").toggle();
            });

            $("#uploaded-videos-summary").click(function() {
                $(".uploaded-videos").toggle();
            });
        });
    </script>
</body>
</html>