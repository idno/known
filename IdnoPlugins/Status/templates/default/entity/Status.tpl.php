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
<div class="">
    <p class="p-name"><?=nl2br($this->parseURLs($this->parseHashtags($this->parseUsers($vars['object']->body, $vars['object']->inreplyto)),$rel))?></p>
</div>
<?= $this->draw('entity/content/embed'); ?>


