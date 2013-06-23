<?php
    $object = $vars['object'];
    /* @var \Idno\Entities\ActivityStreamPost $object */

    if (!empty($object)) {
?>
<div class="row idno-entry">

    <div class="span1 offset1 owner h-card hidden-phone">
        <p>
            <?php $owner = $object->getActor(); ?>
            <a href="<?=$owner->getURL()?>" class="u-url icon-container"><img class="u-photo" src="<?=$owner->getIcon()?>" /></a><br />
            <a href="<?=$owner->getURL()?>" class="p-name u-url"><?=$owner->getTitle();?></a>
        </p>
    </div>
    <div class="span8 h-entry idno-content">
        <div class="visible-phone">
            <p class="p-author author h-card vcard">
                <a href="<?=$owner->getURL()?>"><img class="u-logo logo u-photo photo" src="<?=$owner->getIcon()?>" /></a>
                <a class="p-name fn u-url url" href="<?=$owner->getURL()?>"><?=$owner->getTitle()?></a>
                <a class="u-url" href="<?=$owner->getURL()?>"><!-- This is here to force the hand of your MF2 parser --></a>
            </p>
        </div>
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