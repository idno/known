<?php
    /* @var \knownPlugins\Comic\Comic $object */
    $object = $vars['object'];

    foreach($object->getAttachments() as $attachment) {

?>

        <object title="<?=htmlspecialchars($object->getTitle())?>" data="<?=$attachment['url']?>" type="<?=$attachment['mime-type']?>" width="<?=$object->width?>" height="<?=$object->height?>">
            <?=$this->autop($this->parseHashtags($object->description));?>
        </object>

<?php

    }
?>

<?php echo $this->autop($this->parseHashtags($vars['object']->body)); //TODO: a better rendering algorithm ?>