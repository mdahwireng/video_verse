<?php
$video_views_sql = "SELECT COUNT(*) AS view_count FROM views WHERE video_id = :vid;";
?>