<?php

$retrive_view_ags_sql = "WITH views_ag AS (
	SELECT video_id, COUNT(start) AS vid_views
	FROM views 
	GROUP BY video_id)

SELECT v.id, v.name AS vid_name, th.path AS thumbnail, ac.name AS constraint, v.uploaded_by, v.owned_by,
(CASE
WHEN vid_views IS NULL THEN 0
ELSE vid_views 
END) AS vid_views
FROM videos AS v
LEFT JOIN views_ag AS va
	ON v.id = va.vid_views
INNER JOIN thumbnails AS th 
	ON v.thumbnail = th.id
INNER JOIN accesses AS ac
	ON v.access = ac.id
WHERE v.uploaded_by = :uid OR v.owned_by = :uid;"

?>