<?php
    if (\known\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div class="">
    <p class="p-name"><?=nl2br($this->parseURLs($this->parseHashtags($vars['object']->body),$rel))?></p>
</div>
<?= $this->draw('entity/content/embed'); ?>