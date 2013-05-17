<?php
    $object = $vars['object'];
    /* @var \Idno\Entities\ActivityStreamPost $object */

    if (!empty($object)) {
?>
<div class="row h-entry">

    <div class="span1 owner">
        <p>
            <?php $owner = $object->getActor(); ?>
            <a href="<?=$owner->getURL()?>" class="p-name u-url"><?=$owner->getTitle();?></a>
        </p>
    </div>
    <div class="span8 content">
        <div class="e-content">
            <?php if ($subObject = $object->getObject()) echo $subObject->draw(); ?>
        </div>
        <div class="footer">
            <?php
                if ($object->canEdit()) {
                    echo $this->draw('content/edit');
                }
            ?>
            <?=$this->draw('content/end')?>
        </div>
    </div>

</div>

<?php
    }
?>