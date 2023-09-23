<?php
$video_tags_sql = "SELECT t.tag FROM video_tags AS vt
                    INNER JOIN tags AS t
                        ON vt.tag_id = t.id
                    WHERE video_id = :video_id;";
?>