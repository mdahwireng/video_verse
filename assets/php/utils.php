<?php
function setActiveNav($page, $nav)
{
    $current_page = basename($_SERVER['PHP_SELF'], ".php");
    if ($current_page == $page)
    {
        $hld = "_active";
        $nav = str_replace($page . $hld, 'active', $nav);
    }
    return $nav;
}

// $current_page = basename($_SERVER['PHP_SELF'], ".php");
// echo $current_page;
?>