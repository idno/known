<?php
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>

<div class="panel panel-default notification <?= $notification->isRead() ? 'notification-read' : 'notification-unread' ?>">

    <div class="panel-heading">
        <i class="fa fa-retweet"></i>
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
            Reshared <a href="<?=$post->getDisplayURL();?>"><?=$post->getNotificationTitle()?></a> in <a href="<?= $annotation['permalink'] ?>">this post</a>
        </div>
    </div>
</div>
