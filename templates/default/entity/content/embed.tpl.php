<?php

    // Embedded content code from Aaron Parecki, slightly modified:
    // http://aaronparecki.com/articles/2013/05/09/1/experimenting-with-auto-embedding-content
    $embedded = '';
    if (preg_match_all('/https?:\/\/([^\s]+\.[^\s\.]+\.(png|jpg|jpeg|gif))/i', $vars['object']->body, $matches)) {
        foreach ($matches[0] as $m) {
            $embedded .= '<p><img src="' . $m . '" /></p>';
        }
    }
    if (preg_match_all('/(youtube\.com|youtu\.be)\/watch\?v=([a-z0-9\-\_]+)/i', $vars['object']->body, $matches)) {
        foreach ($matches[2] as $m)
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube.com/embed/' . $m . '"></iframe></div>';
    } else if (preg_match_all('/(youtube\.com|youtu\.be)\/([a-z0-9\-\_]+)/i', $vars['object']->body, $matches)) {
        foreach ($matches[2] as $m)
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube.com/embed/' . $m . '"></iframe></div>';
    }
    if (preg_match_all('/vimeo\.com\/([a-z0-9\-\_]+)/i', $vars['object']->body, $matches)) {
        foreach ($matches[1] as $m)
            $embedded .= '<iframe src="//player.vimeo.com/video/' . $m . '" width="600" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
    echo $embedded;

?>

