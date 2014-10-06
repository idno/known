<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
    if (!empty($vars['object']->tags)) {
        $vars['object']->body .= '<p class="tag-row"><i class="icon-tag"></i>' . $vars['object']->tags . '</p>';
    }
?>
<div><h2 class="p-name"><a href="<?=$vars['object']->getURL()?>"><?=$vars['object']->getTitle()?></a></h2>
<p class="reading">
    <span class="vague"><?php

                $minutes = $vars['object']->getReadingTimeInMinutes();
                echo $minutes . ' min';
             /*   if ($minutes != 1) {
                    echo 's';
                }*/

            ?> read </span>
</p>
<?php echo $this->autop($this->parseURLs($this->parseHashtags($vars['object']->body),$rel)); //TODO: a better rendering algorithm ?></div>