<?php
$videos_sql = "SELECT v.id, v.name AS vid_name, th.path AS thumbnail, ac.name AS constraint  
                FROM videos AS v 
                INNER JOIN thumbnails AS th 
                    ON v.thumbnail = th.id
                INNER JOIN accesses AS ac
                    ON v.access = ac.id;";
?>