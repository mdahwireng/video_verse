<?php
$video_player = '<video class="video-player-frame" controls id="videoElement">
                    <source src=":vpath" type="video/mp4">
                </video>
                <h5>:title</h5>';


$recommended_vid = '<div class="card video-card">
                        <img src=":thumbnail" alt="Video Thumbnail" class="card-img-top thumbnail-image">
                        <span class="views-label">:views Views</span>
                        <div class="play-button-container">
                            <a href="play.php?vid=:vid&tags=:tags"><i class="fas fa-play play-button"></i></a>
                        </div>
                        <div class="video-description">
                            <h5 class="card-title">:title</h5>
                        </div>
                    </div>';

?>