<?php
$explore_row_start_top = '<div class="row align-items-center mt-5">';

$explore_row_start = '<div class="row row-divider align-items-center">';

$explore_row_inner =  '<div class="col-md-3">
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

$explore_row_end = '</div>';
?>