<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="like" class="u-like"';
    } else {
        $rel = '';
    }

    if (!empty($vars['object']->pageTitle)) {
        $body = $vars['object']->pageTitle;
    } else {
        $body = $vars['object']->body;
    }

?>
<div class="">
    <p class="p-name"><i class="icon-star hint"></i> <?=$this->parseURLs(($body),$rel)?> <a href="<?= $vars['object']->body;?>" rel="bookmark nofollow" target="_blank"><i class="icon-link"></i></a></p>
    <?php

        if (!empty($vars['object']->description)) {
        ?>
            <p><small><?=$this->parseURLs($this->parseHashtags($vars['object']->description),$rel)?></small></p>
        <?php
        }

    ?>
</div>
<?php

    // Embedded content code from Aaron Parecki, slightly modified:
    // http://aaronparecki.com/articles/2013/05/09/1/experimenting-with-auto-embedding-content

    $embedded = '';
    if(preg_match_all('/https?:\/\/([^\s]+\.[^\s\.]+\.(png|jpg|jpeg|gif))/i', $vars['object']->body, $matches)) {
        foreach($matches[0] as $m) {
            $embedded .= '<p><img src="' . $m . '" /></p>';
        }
    }
    if(preg_match_all('/(youtube\.com|youtu\.be)\/watch\?v=([a-z0-9]+)/i', $vars['object']->body, $matches)) {
        foreach($matches[2] as $m)
            $embedded .= '<div><iframe class="youtube-player auto-link figure" width="600" height="420" style="border:0"  src="http://www.youtube.com/embed/' . $m . '"></iframe></div>';
    }
    echo $embedded;

?>