<?php
    $object = $vars['object'];
    /* @var \Idno\Entities\ActivityStreamPost $object */

    if (!empty($object)) {
?>
<div class="row h-entry">

    <div class="span owner">
        <p>
            <?php $owner = $object->getActor(); ?>
            <a href="<?=$owner->getURL()?>" class="p-name u-url"><?=$owner->getTitle();?></a>
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