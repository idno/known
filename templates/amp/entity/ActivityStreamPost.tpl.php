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

                <div
                    class="col-md-8 <?= $subObject->getMicroformats2ObjectType() ?> idno-<?= $subObject->getContentTypeCategorySlug() ?> idno-object idno-content">
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
                    <?php if (!empty($subObject)) echo $subObject->draw(); ?>
                    <div class="footer">
                        <?= $this->draw('content/end') ?>
                    </div>
                </div>

            </div>

            <?php
        }
    }
?>
