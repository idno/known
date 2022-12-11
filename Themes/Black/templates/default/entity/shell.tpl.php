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
                    class="col-md-8 col-md-offset-2 <?php echo $object->getMicroformats2ObjectType() ?> idno-<?php echo $object->getContentTypeCategorySlug() ?> idno-object idno-content">
                    <div style="display: none"> <!-- This is useful for webmentions -->
                        <p class="p-author author h-card vcard">
                            <a href="<?php echo $owner->getDisplayURL() ?>" class="icon-container"><img
                                    class="u-logo logo u-photo photo" src="<?php echo $owner->getIcon() ?>"/></a>
                            <a class="p-name fn u-url url" href="<?php echo $owner->getDisplayURL() ?>"><?php echo $owner->getTitle() ?></a>
                            <a class="u-url" href="<?php echo $owner->getDisplayURL() ?>">
                                <!-- This is here to force the hand of your MF2 parser --></a>
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
                                                    <a href="<?php echo $inreplytolink ?>" rel="in-reply-to"
                                                       class="u-in-reply-to">a post on
                                                        <strong><?php echo parse_url($inreplytolink, PHP_URL_HOST); ?></strong>
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
                    <?php echo $this->draw('content/end') ?>
                    </div>
                </div>

            </div>

        <?php
    }
}
