<?php
    if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<p class="p-name e-content entry-content"><data class="p-rsvp" value="<?=$vars['object']->rsvp?>"><strong><?=ucfirst($vars['object']->rsvp)?>:</strong> <?=$this->parseURLs($this->parseHashtags($vars['object']->body),$rel)?></data></p>
<?= $this->draw('entity/content/embed'); ?>