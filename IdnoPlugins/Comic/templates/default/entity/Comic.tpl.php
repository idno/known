<?php
    /* @var \IdnoPlugins\Comic\Comic $object */
    $object = $vars['object'];

foreach($object->getAttachments() as $attachment) {

    ?>

        <p>
            <img src="<?php echo $attachment['url']?>" width="<?php echo $object->width?>" height="<?php echo $object->height?>" alt="<?php echo htmlspecialchars(strip_tags($object->description));?>">
        </p>

    <?php

}
?>

<?php echo $this->autop($this->parseHashtags($vars['object']->body)); //TODO: a better rendering algorithm
