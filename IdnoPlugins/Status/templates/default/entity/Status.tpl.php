<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div class="">
    <p class="p-name"><?=$this->parseURLs($this->parseHashtags($vars['object']->body),$rel)?></p>
    <?php

        if (!empty($vars['object']->inreplyto)) {
        ?>
            <a href="<?=$vars['object']->inreplyto?>" class="u-in-reply-to" rel="in-reply-to"></a>
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