<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div class=""><p><?=$this->parseURLs($this->parseHashtags($vars['object']->body),$rel)?></p></div>