<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div class="h-as-note"><p><?=$this->parseURLs($this->parseHashtags($vars['object']->body),$rel)?></p></div>