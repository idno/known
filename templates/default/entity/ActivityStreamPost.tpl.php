<?php
    $object = $vars['object'];
    $subObject = $object->getObject();
    /* @var \Idno\Entities\ActivityStreamPost $object */
    /* @var \Idno\Common\Entity $subObject */

    if (!empty($object) && !empty($subObject)) {
        if ($owner = $object->getActor()) {
            ?>
            <div class="row idno-entry idno-entry-<?php
                if (preg_match('@\\\\([\w]+)$@', get_class($subObject), $matches)) {
                    echo strtolower($matches[1]);
                }?>">

                <div class="col-md-1 col-md-offset-1 owner h-card visible-md visible-lg">
                    <p>
                        <a href="<?= $owner->getDisplayURL() ?>" class="u-url icon-container">
	                        <img class="u-photo" src="<?= $owner->getIcon() ?>"/></a><br/>
                        <a href="<?= $owner->getDisplayURL() ?>" class="p-name u-url fn"><?= htmlentities(strip_tags($owner->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
                    </p>
                </div>
                
                <div
                    class="col-md-8 <?= $subObject->getMicroformats2ObjectType() ?> idno-<?= $subObject->getContentTypeCategorySlug() ?> idno-object idno-content">
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
                        if (($subObject->inreplyto)) {
                            ?>
                            <div class="reply-text">
                                <?php

                                    if (($subObject->replycontext)) {
                                    } else {

                                        if (!is_array($subObject->inreplyto)) {
                                            $inreplyto = array($subObject->inreplyto);
                                        } else {
                                            $inreplyto = $subObject->inreplyto;
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
                    <div class="e-content entry-content">
                        <?php if (!empty($subObject)) echo $subObject->draw(); ?>
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
