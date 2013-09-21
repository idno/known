<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div class="">
    <p class="p-name"><data class="p-rsvp" value="<?=$vars['object']->rsvp?>"><strong><?=ucfirst($vars['object']->rsvp)?>:</strong> <?=$this->parseURLs($this->parseHashtags($vars['object']->body),$rel)?></data></p>
</div>
<?= $this->draw('entity/content/embed'); ?>