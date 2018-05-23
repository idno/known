<?php
    $tags = '';
    $rel = '';
    /*if (\Idno\Core\Idno::site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }*/
    if (!empty($vars['object']->tags)) {
        $tags = $this->__(['tags' => $vars['object']->tags])->draw('forms/output/tags');
    }

?>
<p class="p-name e-content entry-content"><?= nl2br($this->parseURLs($this->parseHashtags($this->parseUsers(htmlentities($vars['object']->body, ENT_QUOTES, 'UTF-8') . $tags, $vars['object']->inreplyto)), $rel)) ?></p>
<?php
    if (!substr_count(strtolower($vars['object']->body), '<img')) {
        echo $this->draw('entity/content/embed');
    }
?>