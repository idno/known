<?php
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>


<div class="panel panel-default notification <?= $notification->isRead() ? 'notification-read' : 'notification-unread' ?>">
    <div class="panel-heading">
        <i class="fa fa-star"></i>
        <?= $notification->getMessage() ?>
        <time datetime="<?= date('c', $notification->created) ?>"><?= strftime('%c', $notification->created) ?></time>
    </div>
    <div class="panel-body">
        <div class="notification-avatar">
            <a href="<?=$annotation['owner_url']?>">
                <img src="<?=$annotation['owner_image']?>">
                <a href="<?=$annotation['owner_url']?>"><?=$annotation['owner_name']?></a>
            </a>
        </div>

        <div class="notification-body" >
            Liked <a href="<?=$post->getDisplayURL();?>"><?=$post->getNotificationTitle()?></a>
            in <a href="<?=$annotation['permalink']?>">this post</a>
        </div>

        <form action="<?= $notification->getURL() ?>" method="POST">
            <input type="hidden" name="read"  value="true">
            <?= \Idno\Core\Idno::site()->actions()->signForm('/account/notifications') ?>
        </form>

    </div>
</div>
