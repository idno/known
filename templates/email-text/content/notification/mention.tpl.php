<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$target       = $notification->getTarget();
?>
Hi! We wanted to let you know that *<?=$annotation['owner_name']?>* mentioned you on *<?=$annotation['permalink']?>*