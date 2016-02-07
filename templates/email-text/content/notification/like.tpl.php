<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>
Hi! We wanted to let you know that *<?=$annotation['owner_name']?>* liked the post *<?=$post->getNotificationTitle()?>*<br>

View post: <?=$post->getDisplayURL()?>
