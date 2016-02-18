<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>
Hi! We wanted to let you know that *<?=$annotation['owner_name']?>* RSVPed to the event *<?=$post->getNotificationTitle()?>*.

Here's what they said:

> <?= strip_tags(preg_replace('#<br\s*/?>#i', "\n> ", str_replace("\n", "\n> ", $annotation['content']))); ?>

<?php

    if (!empty($post)) {

        ?>
View post: <?=$post->getDisplayURL()?>
    <?php

    }

?>