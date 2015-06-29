<?php

    // Embedded content code from Aaron Parecki, slightly modified:
    // http://aaronparecki.com/articles/2013/05/09/1/experimenting-with-auto-embedding-content
    $embedded = '';

    $body = Idno\Core\site()->triggerEvent('url/expandintext', ['object' => $vars['object']], $vars['object']->body);

    if (preg_match_all('/https?:\/\/([^\s]+\.[^\s\.]+\.(png|jpg|jpeg|gif))/i', $body, $matches)) {
        foreach ($matches[0] as $m) {
            $embedded .= '<p><img src="' . $m . '" /></p>';
        }
    }
    if (preg_match_all('/(youtube\.com|youtu\.be)\/watch\?v=([a-z0-9\-\_]+)/i', $body, $matches)) {
        foreach ($matches[2] as $m)
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube.com/embed/' . $m . '"></iframe></div>';
    } else if (preg_match_all('/(youtube\.com|youtu\.be)\/([a-z0-9\-\_]+)/i', $body, $matches)) {
        foreach ($matches[2] as $m)
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="//www.youtube.com/embed/' . $m . '"></iframe></div>';
    }
    if (preg_match_all('/vimeo\.com\/([0-9]+)/i', $body, $matches)) {
        foreach ($matches[1] as $m)
            $embedded .= '<iframe src="//player.vimeo.com/video/' . $m . '" width="600" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
    if (preg_match_all('/https?:\/\/twitter\.com\/[^\s]+\/status\/[^\s]+\/?/i', $body, $matches)) {

        foreach ($matches[0] as $m)
            $embedded .= '<div id="sc_' . md5($m) . '" class="twitter-embed" data-url="' . $m . '"></div>';
    }
    //Soundcloud
    if (preg_match_all('/https?:\/\/soundcloud\.com\/[^\s]+\/status\/[^\s]+\/?/i', $body, $matches)){
        print_r($matches);
        foreach ($matches[0])
        //Get the SoundCloud URL 
        $url="https://soundcloud.com/epitaph-records/this-wild-life-history";
        //Get the JSON data of song details with embed code from SoundCloud oEmbed
        $getValues=file_get_contents('http://soundcloud.com/oembed?format=js&url='.$m.'&iframe=true');
        //Clean the Json to decode
        $decodeiFrame=substr($getValues, 1, -2);
        //json decode to convert it as an array
        $jsonObj = json_decode($decodeiFrame);
        $embedded .= $jsonObj->html;
    }
    echo $embedded;


