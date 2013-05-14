<?php
    $object = $vars['object'];
    /* @var \Idno\Entities\ActivityStreamPost $object */

    if (!empty($object)) {
?>
<div class="entry row span11">

    <div class="span1 owner">
        <p>
            <?php $owner = $object->getActor(); ?>
            <a href="<?=$owner->getURL()?>"><?=$owner->getTitle();?></a>
        </p>
    </div>
    <div class="span8 content">
        <?php if ($subObject = $object->getObject()) echo $subObject->draw(); ?>
        <?php
            if ($object->canEdit()) {
                echo $this->draw('content/edit');
            }
        ?>
    </div>

</div>

<?php
    }
?>