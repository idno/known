<?php
    $annotation = $notification->getObject();
    $post       = $notification->getTarget();

if (!empty($post)) {
    ?>

        <div class="row idno-entry idno-entry-notification-reply notification <?php echo $notification->isRead() ? 'notification-read' : 'notification-unread' ?>">
            <div class="col-md-1 col-md-offset-1 owner h-card visible-md visible-lg">
            <?php if (isset($annotation['owner_image'])) { ?>
                    <p>
                        <a href="<?php echo $annotation['owner_url'] ?>" class="u-url icon-container">
                            <img class="u-photo" src="<?php echo $annotation['owner_image'] ?>"/></a><br/>
                    </p>
            <?php } ?>
            </div>
            <div
                class="col-md-8 idno-notification-reply idno-object idno-content">
            <?php if (empty($vars['hide-body'])) { ?>
                    <div class="e-content entry-content">
                        <?php if (!empty($annotation['content'])) {
                            $this->autop($this->parseURLs($this->parseHashtags($this->parseUsers(htmlentities($annotation['content'], ENT_QUOTES, 'UTF-8')))));
} ?>
                    </div>
            <?php } ?>
                <div class="footer">
                    <div class="permalink">
                            <?php /*<span class="notification-icon"><?= $icon ?></span> */ ?>
                            <a href="<?php echo $annotation['owner_url'] ?>"><?php echo $annotation['owner_name'] ?></a> <?php echo $interaction ?>
                            <a href="<?php echo $post->getDisplayURL(); ?>"><?php echo \Idno\Core\Idno::site()->template()->sampleTextChars($post->getNotificationTitle(), 60); ?></a>
                            <time datetime="<?php echo date('c', $notification->created) ?>"
                                  class="dt-published"><?php echo strftime('%c', $notification->created) ?></time>                 
                        <?php

                        if (!empty($verb)) {

                            ?>
                            <a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>status/edit?url=<?php echo urlencode($annotation['permalink']) ?>"
                                   class="edit pull-right"><i class="far fa-comment-dots"> </i> <?php echo $verb ?></a>                
                                <?php

                        }

                        ?>
                    </div>
                </div>
            </div>
            <form action="<?php echo $notification->getURL() ?>" method="POST">
                <input type="hidden" name="read" value="true">
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/account/notifications') ?>
            </form>
        </div>

        <?php

}

    unset($this->vars['hide-body']);
