<?php
    if (\Idno\Core\site()->currentPage()->isPermalink()) {
        $rel = 'rel="in-reply-to"';
    } else {
        $rel = '';
    }
?>
<p class="p-name"><a href="<?=$vars['object']->getURL();?>"><?=$vars['object']->getTitle();?></a></p>
<?php
    if ($attachments = $vars['object']->getAttachments()) {
        foreach($attachments as $attachment) {
            var_export($attachment);
            $mainsrc= $attachment['url'];//\Idno\Core\site()->config()->url . 'file/' . $attachment['_id'];
?>
            <p style="text-align: center">
                <video src="<?=$mainsrc?>" class="u-video" controls preload="metadata"></video><br />
                <small><a href="<?=$mainsrc?>">Direct link</a></small>
            </p>
<?php
        }
    }
?>
<?=$this->autop($this->parseHashtags($this->parseURLs($vars['object']->body, $rel)))?>