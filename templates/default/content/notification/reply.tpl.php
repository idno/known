<?php
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>

<div class="notification-avatar">
    <a href="<?=$annotation['owner_url']?>">
        <img src="<?=$annotation['owner_image']?>">
        <a href="<?=$annotation['owner_url']?>"><?=$annotation['owner_name']?></a>
    </a>
</div>

<div class="notification-body" >
 in reply to <a href="<?=$post->getDisplayURL();?>"><?=$post->getNotificationTitle()?></a>

<blockquote>
    <?=$annotation['content']?>
</blockquote>
</div>
