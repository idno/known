<?php
    $tags = '';
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
    if (!empty($vars['object']->tags)) {
        /*$vars['object']->body .= */ $tags = '<p class="tag-row"><i class="icon-tag"></i>' . $vars['object']->tags . '</p>';
    }

?>
<div class="">
    <p class="p-name"><?= nl2br($this->parseURLs($this->parseHashtags($this->parseUsers(htmlentities($vars['object']->body, ENT_QUOTES, 'UTF-8') . $tags, $vars['object']->inreplyto)), $rel)) ?></p>
</div>
<?php
    if (!substr_count(strtolower($vars['object']->body), '<img')) {
        echo $this->draw('entity/content/embed');
    }
?>