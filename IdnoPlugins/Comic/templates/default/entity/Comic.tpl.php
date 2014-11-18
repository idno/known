<?php
    /* @var \IdnoPlugins\Comic\Comic $object */
    $object = $vars['object'];

    foreach($object->getAttachments() as $attachment) {

?>

        <p>
            <img src="<?=$attachment['url']?>" width="<?=$object->width?>" height="<?=$object->height?>" alt="<?=htmlspecialchars(strip_tags($object->description));?>">
        </p>

<?php

    }
?>

<?php echo $this->autop($this->parseHashtags($vars['object']->body)); //TODO: a better rendering algorithm ?>