<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
    if (!empty($vars['object']->tags)) {
        $vars['object']->body .= '<p class="tag-row"><i class="icon-tag"></i>' . $vars['object']->tags . '</p>';
    }
?>
    <h2 class="p-photo"><a href="<?= $vars['object']->getURL(); ?>"><?= $vars['object']->getTitle(); ?></a></h2>
<?php
    if ($attachments = $vars['object']->getAttachments()) {
        foreach ($attachments as $attachment) {
            //$mainsrc= \Idno\Core\site()->config()->url . 'file/' . $attachment['_id'];
            $mainsrc = $attachment['url'];
            if (!empty($vars['object']->thumbnail_large)) {
                $src = $vars['object']->thumbnail_large;
            } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                $src = $vars['object']->thumbnail;
            } else {
                $src = $mainsrc;
            }
            ?>
            <p style="text-align: center">
                <a href="<?= $mainsrc ?>"><img src="<?= $src ?>" class="u-photo"/></a>
            </p>
        <?php
        }
    }
?>
<?= $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel))) ?>