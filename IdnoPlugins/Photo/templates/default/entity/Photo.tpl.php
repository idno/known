<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
?>
<p><a href="<?=$vars['object']->getURL();?>"><?=$vars['object']->getTitle();?></a></p>
<?php
    if ($attachments = $vars['object']->getAttachments()) {
        foreach($attachments as $attachment) {
            if (!empty($vars['object']->thumbnail)) {
                $src = $vars['object']->thumbnail;
            } else {
                $src = \Idno\Core\site()->config()->url . 'file/' . $attachment['_id'];
            }
?>
            <p>
                <img src="<?=$src?>" class="u-photo" />
            </p>
<?php
        }
    }
?>
<p><?=$this->parseURLs($vars['object']->body, $rel)?></p>