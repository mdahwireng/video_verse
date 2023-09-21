<?php
    session_start();
    unset($_SESSION['name']);
    unset($_SESSION['user_id']);
    unset($_SESSION['initials']);
    header('Location: index.php');
?>