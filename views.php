<?php
require_once "assets/php/connections/pdo.php";
require_once "assets/php/sql_strings/add_video_views.php";

//check if post is set

if (isset($_POST['videoId'])){
    //get user

    $user_id = $_SESSION['user_id'] ?? -1;

    //get video id and convert to interger
    $video_id = (int) $_POST['videoId'];


    //get start time
    $start = $_POST['start'];

    //convert start time to datetime
    $start = date("Y-m-d H:i:s", $start);


    //add view
    $stmt = $conn->prepare($add_video_views_sql);
    $stmt->execute(array(":user_id" => $user_id, ":video_id" => $video_id, ":start" => $start));


}

?>