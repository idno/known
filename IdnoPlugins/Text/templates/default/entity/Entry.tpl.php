<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div><h2 class="p-name"><a href="<?=$vars['object']->getURL()?>"><?=$vars['object']->getTitle()?></a></h2>
<p>
    <span class="vague">Reading time: <?php

                $minutes = $vars['object']->getReadingTimeInMinutes();
                echo $minutes . ' minute';
                if ($minutes != 1) {
                    echo 's';
                }

            ?></span>
</p>
<?php echo $this->autop($this->parseURLs($this->parseHashtags($vars['object']->body),$rel)); //TODO: a better rendering algorithm ?></div>