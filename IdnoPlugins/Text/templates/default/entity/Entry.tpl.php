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
<div>
    <?php
        if (\Idno\Core\site()->template()->getTemplateType() == 'default') {
            ?>
            <h2 class="p-name"><a
                    href="<?= $vars['object']->getURL() ?>"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
            </h2>
        <?php

        }

    ?>
    <p class="reading">
        <span class="vague"><?php

                    $minutes = $vars['object']->getReadingTimeInMinutes();
                    echo $minutes . ' min';

                ?> read </span>
    </p>
    <?php echo $this->autop($this->parseURLs($this->parseHashtags($vars['object']->body),$rel)); //TODO: a better rendering algorithm ?>
</div>