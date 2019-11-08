<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>
<?php echo $annotation['owner_name']?> RSVPed <?php echo $annotation['object']->getNotificationTitle()?>.
?>
<?php
    unset($this->vars['notification']);
