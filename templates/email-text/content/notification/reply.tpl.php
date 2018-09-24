<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>
Hi! We wanted to let you know that *<?php echo $annotation['owner_name']?>* replied to the post *<?php echo $post->getNotificationTitle()?>*.

Here's what they said:

> <?php echo strip_tags(preg_replace('#<br\s*/?>#i', "\n> ", str_replace("\n", "\n> ", $annotation['content']))); ?>

<?php

if (!empty($post)) {

    ?>

    View post: <?php echo $post->getDisplayURL()?>
    <?php

}

?>
<?php
    unset($this->vars['notification']);
