<?php
    $object = $vars['object'];
    /* @var \Idno\Common\Entity $object */

    if (!empty($object)) {
        if ($owner = $object->getOwner()) {
            ?>
            <div class="row idno-entry idno-entry-<?php
                if (preg_match('@\\\\([\w]+)$@', get_class($object), $matches)) {
                    echo strtolower($matches[1]);
                }?>">
                <div
                    class="col-md-8 col-md-offset-2 <?= $object->getMicroformats2ObjectType() ?> idno-<?= $object->getContentTypeCategorySlug() ?> idno-object idno-content">
                    <div>
                        <div class="p-author author h-card vcard">
                            <a href="<?= $owner->getDisplayURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                                src="<?= $owner->getIcon() ?>"/></a>
                            <a class="p-name fn u-url url"
                               href="<?= $owner->getDisplayURL() ?>"><?= $owner->getTitle() ?></a>
                            <a class="u-url" href="<?= $owner->getDisplayURL() ?>">
                                <!-- This is here to force the hand of your MF2 parser --></a>
                        </div>
                        <div class="break">&nbsp;</div>
                    </div>
                    <div class="datestamp">
                        <p>
                            <a class="u-url url" href="<?= $object->getDisplayURL() ?>" rel="permalink">
                                <time class="dt-published"
                                      datetime="<?= date('c', $object->created) ?>"><?= date('F j, Y', $object->created) ?></time>
                            </a>
                        </p>
                    </div>
                    <?php
                        if (($object->inreplyto)) {
                            ?>
                            <div class="reply-text">
                                <?php

                                    if (($object->replycontext)) {
                                    } else {

                                        if (!is_array($object->inreplyto)) {
                                            $inreplyto = [$object->inreplyto];
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
