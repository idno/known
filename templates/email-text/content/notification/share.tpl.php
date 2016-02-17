<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>
Hi! We wanted to let you know that *<?=$annotation['owner_name']?>* reshared the post *<?=$post->getNotificationTitle()?>*

View post: <?=$post->getDisplayURL()?>
