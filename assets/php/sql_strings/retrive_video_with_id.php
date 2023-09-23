<?php
$videos_id_sql = "SELECT v.name AS vid_name, v.path As vid_path
                FROM videos AS v 
                WHERE v.id = :vid;";
?>