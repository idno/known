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
?>
            <p>
                <img src="<?=\Idno\Core\site()->config()->url . 'file/' . $attachment['_id']?>" class="u-photo" />
            </p>
<?php
        }
    }
?>
<p><?=$this->parseURLs($vars['object']->body, $rel)?></p>