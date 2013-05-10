<?php
    $object = $vars['object'];
    /* @var \Idno\Entities\ActivityStreamPost $object */
?>
<div class="entry row span11">

    <div class="span1 owner">
        <p>
            <?php $owner = $object->getActor(); ?>
            <a href="<?=$owner->getURL()?>"><?=$owner->getTitle();?></a>
        </p>
    </div>
    <div class="span8 content">
        <?=$object->getObject()->draw()?>
        <?php
            if ($object->canEdit()) {
                echo $this->draw('content/edit');
            }
        ?>
    </div>

</div>