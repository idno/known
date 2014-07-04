<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to" class="u-in-reply-to"';
    } else {
        $rel = '';
    }
?>
<div><h2 class="p-name"><a href="<?=$vars['object']->getURL()?>"><?=$vars['object']->getTitle()?></a></h2>
<?php echo $this->autop($this->parseURLs($this->parseHashtags($vars['object']->body),$rel)); //TODO: a better rendering algorithm ?></div>