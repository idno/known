<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div itemscope itemtype="http://schema.org/Article">
    <div><h2 class="p-name">
        <a href="<?=$vars['object']->getURL()?>">
            <span itemprop="name"> <?=$vars['object']->getTitle()?></span>
        </a>
    </h2></div>
    <div><?=$vars['object']->getImage()?></div>
    <p>
        <span class="vague">Reading time: <?php

                    $minutes = $vars['object']->getReadingTimeInMinutes();
                    echo $minutes . ' minute';
                    if ($minutes != 1) {
                        echo 's';
                    }

                ?></span>
    </p>
</div>
<?php echo $this->autop($this->parseURLs($this->parseHashtags($vars['object']->body),$rel)); //TODO: a better rendering algorithm ?></div>