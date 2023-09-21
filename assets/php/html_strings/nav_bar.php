<?php
session_start();

$signed_in_nav_bar = '<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand" href="#">VideoVerse</a>

<!-- Toggler/collapsible Button -->
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="#">Explore</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">History</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Upload</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </li>
        <!-- Display user initials in a circular frame when signed in -->
        <li class="nav-item">
            <div class="user-initials">:UI</div>
        </li>
    </ul>
</div>
</nav>
' ;

$signed_out_nav_bar =  '<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand" href="#">VideoVerse</a>

<!-- Toggler/collapsible Button -->
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="#">Explore</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Sign In</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="signup.php">Sign Up</a>
        </li>
    </ul>
</div>
</nav>
';

if (isset($_SESSION['name']))
{
    $nav = $signed_in_nav_bar;
    $nav = str_replace(':UI', $_SESSION['initials'], $nav);
}
else
{
    $nav =  $signed_out_nav_bar;
}