<?php
    $annotation = $notification->getObject();
    $post       = $notification->getTarget();

    if (!empty($post)) {
        ?>

        <div class="row idno-entry idno-entry-notification-reply notification <?= $notification->isRead() ? 'notification-read' : 'notification-unread' ?>">
            <div class="col-md-1 col-md-offset-1 owner h-card visible-md visible-lg">
                <?php if (isset($annotation['owner_image'])) { ?>
                    <p>
                        <a href="<?= $annotation['owner_url'] ?>" class="u-url icon-container">
                            <img class="u-photo" src="<?= $annotation['owner_image'] ?>"/></a><br/>
                    </p>
                <?php } ?>
            </div>
            <div
                class="col-md-8 idno-notification-reply idno-object idno-content">
                <div class="e-content entry-content">
                    <?= $this->autop($annotation['content']); ?>
                </div>
                <div class="footer">
                    <div class="permalink">
                        <span class="notification-icon"><?= $icon ?></span>
                        <a href="<?= $annotation['owner_url'] ?>"><?= $annotation['owner_name'] ?></a> <?= $interaction ?>
                        <a href="<?= $post->getDisplayURL(); ?>"><?= $post->getNotificationTitle() ?></a>
                        <time datetime="<?= date('c', $notification->created) ?>"
                              class="dt-published"><?= strftime('%c', $notification->created) ?></time>
                        <?php

                            if (!empty($verb)) {

                                ?>
                                <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>status/edit?url=<?= urlencode($annotation['permalink']) ?>"
                                   class="edit"><?= $verb ?></a>
                                <?php

                            }

                        ?>
                    </div>
                </div>
            </div>
            <form action="<?= $notification->getURL() ?>" method="POST">
                <input type="hidden" name="read" value="true">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/account/notifications') ?>
            </form>
        </div>

        <?php

    }
