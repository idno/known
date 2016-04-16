<?php
    $object = $vars['object'];
    /* @var \Idno\Common\Entity $object */

    if ($object) {
        if ($owner = $object->getOwner()) {
            ?>
            <div class="row idno-entry idno-entry-<?php
                if (preg_match('@\\\\([\w]+)$@', get_class($object), $matches)) {
                    echo strtolower($matches[1]);
                }?> <?= $object->getMicroformats2ObjectType() ?> idno-<?= $object->getContentTypeCategorySlug() ?> idno-object">

                <div class="col-md-1 col-md-offset-1 owner p-author h-card visible-md visible-lg">
                    <p>
                        <a href="<?= $owner->getDisplayURL() ?>" class="u-url icon-container">
	                        <img class="u-photo" src="<?= $owner->getIcon() ?>"/></a><br/>
                        <a href="<?= $owner->getDisplayURL() ?>" class="p-name u-url fn"><?= htmlentities(strip_tags($owner->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
                    </p>
                </div>

                <div class="col-md-8 idno-content">
                    <!--<div class="visible-xs">
                        <p class="p-author author h-card vcard">
                            <a href="<?= $owner->getDisplayURL() ?>" class="icon-container"><img
                                    class="u-logo logo u-photo photo" src="<?= $owner->getIcon() ?>"/></a>
                            <a class="p-name fn u-url url" href="<?= $owner->getDisplayURL() ?>"><?= htmlentities(strip_tags($owner->getTitle()), ENT_QUOTES, 'UTF-8') ?></a>
                            <a class="u-url" href="<?= $owner->getDisplayURL() ?>">
                                </a>
                        </p>
                    </div>-->
                    <?php
                        if ($object->inreplyto) {
                            ?>
                            <div class="reply-text">
                                <?php

                                    if (($object->replycontext)) {
                                    } else {

                                        if (!is_array($object->inreplyto)) {
                                            $inreplyto = array($object->inreplyto);
                                        } else {
                                            $inreplyto = $object->inreplyto;
                                        }

                                        if (!empty($inreplyto)) {
                                            ?>

                                            <p>
                                                <i class="fa fa-reply"></i> Replied to
                                                <?php

                                                    $replies = 0;
                                                    foreach ($inreplyto as $inreplytolink) {
                                                        if ($replies > 0) {
                                                            if (sizeof($inreplyto) > 2 && $replies < sizeof($inreplyto) - 1) {
                                                                echo ', ';
                                                            } else {
                                                                echo ' and ';
                                                            }
                                                        }
                                                        ?>
                                                    <a href="<?= $inreplytolink ?>" rel="in-reply-to"
                                                       class="u-in-reply-to">a post on
                                                        <strong><?= parse_url($inreplytolink, PHP_URL_HOST); ?></strong>
                                                        </a><?php
                                                        $replies++;
                                                    }

                                                ?>:
                                            </p>

                                        <?php
                                        }

                                    }

                                ?>
                            </div>
                        <?php
                        }

                    ?>
                    <div class="idno-body">
                      <?php if (!empty($object)) echo $object->draw(); ?>
                    </div>
                    <div class="footer">
                        <?= $this->draw('content/end') ?>
                    </div>
                </div>

            </div>

        <?php
        }
    }
?>
