<?php
    $object = $vars['object'];
    /* @var \Idno\Entities\ActivityStreamPost $object */

    if (!empty($object)) {
?>
<div class="row ">

    <div class="span1 offset1 owner h-card">
        <p>
            <?php $owner = $object->getActor(); ?>
            <a href="<?=$owner->getURL()?>" class="u-url icon-container hidden-phone"><img class="u-photo" src="<?=$owner->getIcon()?>" /></a><br />
            <a href="<?=$owner->getURL()?>" class="p-name u-url"><?=$owner->getTitle();?></a>
        </p>
    </div>
    <div class="span8 content h-entry">
        <div class="e-content entry-content">
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