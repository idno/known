<?php
    if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
    if (!empty($vars['object']->tags)) {
        $vars['object']->body .= '<p class="tag-row"><i class="icon-tag"></i>' . $vars['object']->tags . '</p>';
    }
    if (empty($vars['feed_view'])) {
        ?>
        <h2 class="photo-title p-name"><a
                href="<?= $vars['object']->getDisplayURL(); ?>"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
        </h2>
        <?php
    }
    if ($attachments = $vars['object']->getAttachments()) {
        foreach ($attachments as $attachment) {
            //$mainsrc= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'file/' . $attachment['_id'];
            $mainsrc = $attachment['url'];
            $filename = "";
            if (!empty($attachment['filename']))
                $filename = $attachment['filename'];
            if (!empty($vars['object']->thumbs_large) && !empty($vars['object']->thumbs_large[$filename])) {
                $src = $vars['object']->thumbs_large[$filename]['url'];

            // Old style
            } else if (!empty($vars['object']->thumbnail_large)) {
                $src = $vars['object']->thumbnail_large;
            } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                $src = $vars['object']->thumbnail;
            } else {
                $src = $mainsrc;
            }

            $src = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($src);
            $mainsrc = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($mainsrc);

            // Patch to correct certain broken URLs caused by https://github.com/idno/known/issues/526
            $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $src);
            $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $mainsrc);

            if (list($width, $height, $type, $attr) = getimagesize($src)) {
                ?>
                <p style="text-align: center">
                    <a href="<?= $this->makeDisplayURL($mainsrc) ?>"><img src="<?= $this->makeDisplayURL($src) ?>" class="u-photo" width="<?=$width?>" height="<?=$height?>"/></a>
                </p>
                <?php
            }

        }
    }
?>
<?= $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel))) ?>
