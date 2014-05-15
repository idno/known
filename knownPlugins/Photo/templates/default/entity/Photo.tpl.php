<?php
    if (\known\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
?>
<p class="p-name"><a href="<?=$vars['object']->getURL();?>"><?=$vars['object']->getTitle();?></a></p>
<?php
    if ($attachments = $vars['object']->getAttachments()) {
        foreach($attachments as $attachment) {
            $mainsrc= \known\Core\site()->config()->url . 'file/' . $attachment['_id'];
            if (!empty($vars['object']->thumbnail_large)) {
                $src = $vars['object']->thumbnail_large;
	    } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                $src = $vars['object']->thumbnail;
            } else {
                $src = $mainsrc;
            }
?>
            <p style="text-align: center">
                <a href="<?=$mainsrc?>"><img src="<?=$src?>" class="u-photo" /></a>
            </p>
<?php
        }
    }
?>
<?=$this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel)))?>