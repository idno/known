<?php
$attachments = $vars['object']->getAttachments();

$src = "";
if (count($attachments)) {
    $src = $attachments[0]['url'];
} 

?><div class="oauth2-client">
    <a href="<?= $vars['object']->getURL(); ?>"><img src="<?= $src; ?>" alt="<?= $vars['object']->label; ?>" /></a>
</div>