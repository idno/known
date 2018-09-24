<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$target       = $notification->getTarget();
?>
Hi! We wanted to let you know that *<?php echo $annotation['owner_name']?>* mentioned you on *<?php echo $annotation['permalink']?>*

<?php
    unset($this->vars['notification']);
