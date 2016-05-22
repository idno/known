<?php

    // Embedded content code from Aaron Parecki, slightly modified:
    // http://aaronparecki.com/articles/2013/05/09/1/experimenting-with-auto-embedding-content
    $embedded = '';

    $body = $vars['value'];

    if (preg_match_all('/https?:\/\/([^\s]+\.[^\s\.]+\.(png|jpg|jpeg|gif))/i', $body, $matches)) {
        foreach ($matches[0] as $m) {
            $embedded .= '<p><img src="' . $m . '" /></p>';
        }
    }
    if (preg_match_all('/(youtube\.com|youtu\.be)\/watch\?v=([a-z0-9\-\_]+)/i', $body, $matches)) {
        foreach ($matches[2] as $m) {
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube-nocookie.com/embed/' . $m . '"></iframe></div>';
        }
    } else if (
        preg_match_all('/(youtube\.com|youtu\.be)\/c\/([a-z0-9\-\_]+)/i', $body, $matches) ||
        preg_match_all('/(youtube\.com|youtu\.be)\/channel\/([a-z0-9\-\_]+)/i', $body, $matches)
    ) {
        foreach ($matches[2] as $m) {
            // TODO: see if there's a way to embed YouTube channels
        }
    } else if (preg_match_all('/(youtube\.com|youtu\.be)\/user\/([a-z0-9\-\_]+)/i', $body, $matches)) {
        foreach ($matches[2] as $m) {
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube-nocookie.com/embed/?listType=user_uploads&list=' . $m . '"></iframe></div>';
        }
    } else if (preg_match_all('/(youtube\.com|youtu\.be)\/([a-z0-9\-\_]+)/i', $body, $matches)) {
        foreach ($matches[2] as $m) {
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube-nocookie.com/embed/' . $m . '"></iframe></div>';
        }
    }
    if (preg_match_all('/vimeo\.com\/([0-9]+)/i', $body, $matches)) {
        foreach ($matches[1] as $m) {
            $embedded .= '<iframe src="//player.vimeo.com/video/' . $m . '" width="600" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }
    }
    if (preg_match_all('/https?:\/\/twitter\.com\/[^\s]+\/status\/[^\s]+\/?/i', $body, $matches)) {

        foreach ($matches[0] as $m)
            $embedded .= '<div id="sc_' . md5($m) . '" class="twitter-embed" data-url="' . $m . '"></div>';
    }
    if (preg_match_all('/https?:\/\/soundcloud\.com\/[^\s]+\/?/i', $body, $matches)) {

        foreach ($matches[0] as $m)
            $embedded .= '<div id="sc_'.md5($m).'" class="soundcloud-embed" data-url="'.$m.'"></div>';
    }

    echo $embedded;


