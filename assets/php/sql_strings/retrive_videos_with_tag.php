<?php
$videos_with_tags_sql = "SELECT v.id, v.name AS vid_name, th.path AS thumbnail, ac.name AS constraint  
                FROM videos AS v 
                INNER JOIN thumbnails AS th 
                    ON v.thumbnail = th.id
                INNER JOIN accesses AS ac
                    ON v.access = ac.id
                INNER JOIN video_tags AS vt
                    ON v.id = vt.video_id
                INNER JOIN tags AS t
                    ON vt.tag_id = t.id
                WHERE :tag_where_clause;";

?>