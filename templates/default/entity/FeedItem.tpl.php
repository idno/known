<?php

    $item = $vars['object'];
    /* @var \Idno\Entities\Reader\FeedItem $item */

?>

<div class="row idno-entry idno-entry-<?php
    if (preg_match('@\\\\([\w]+)$@', get_class($item), $matches)) {
        echo strtolower($matches[1]);
    }?>">

    <div class="col-md-1 col-md-offset-1 owner h-card hidden-sm">
        <p>
            <a href="<?= $item->getAuthorURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                src="<?= $item->getAuthorPhoto()?>"/></a><br/>
            <a href="<?= $item->getAuthorURL() ?>" class="p-name u-url fn"><?= $item->getAuthorName(); ?></a>
        </p>
    </div>
    <div
        class="col-md-8 idno-feed-item idno-object idno-content">
        <div class="visible-sm">
            <p class="p-author author h-card vcard">
                <a href="<?= $item->getAuthorURL() ?>" class="icon-container"><img
                        class="u-logo logo u-photo photo" src="<?= $item->getAuthorPhoto() ?>"/></a>
                <a class="p-name fn u-url url" href="<?= $item->getAuthorURL() ?>"><?= $item->getAuthorName() ?></a>
            </p>
        </div>
        <div class="idno-body">
            <?=$this->autop($item->getBody())?>
        </div>
        <div class="footer">
            <?= $this->draw('content/feed/end') ?>
        </div>
    </div>
</div>