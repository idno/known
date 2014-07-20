<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div class="">
    <p class="p-name"><?=nl2br($this->parseURLs($this->parseHashtags($this->parseUsers($vars['object']->body, $vars['object']->inreplyto)),$rel))?></p>
</div>
<?= $this->draw('entity/content/embed'); ?>