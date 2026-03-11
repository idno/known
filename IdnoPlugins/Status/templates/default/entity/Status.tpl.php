<?php
    $tags = '';
    $rel = '';
if (!empty($vars['object']->tags)) {
    $tags = $this->__(['tags' => $vars['object']->tags])->draw('forms/output/tags');
}

?>
<p class="p-name e-content entry-content"><?php echo nl2br($this->parseURLs($this->parseHashtags($this->parseUsers(htmlentities(html_entity_decode($vars['object']->body), ENT_QUOTES, 'UTF-8') . $tags, $vars['object']->inreplyto)), $rel)) ?></p>
<?php

// Render cached link preview if available (server-side, no JS needed)
if (!empty($vars['object']->link_preview) && empty($vars['object']->hide_preview)) {
    echo $this->__(['preview' => $vars['object']->link_preview])->draw('entity/LinkPreviewCard');
} elseif (!substr_count(strtolower($vars['object']->body), '<img')) {
    // Fall back to JS-based embed/unfurl if no cached preview yet
    echo $this->draw('entity/content/embed');
}
