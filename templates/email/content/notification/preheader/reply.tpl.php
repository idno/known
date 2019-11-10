<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>
<?php echo $annotation['owner_name']?> replied to <?php echo $post->getNotificationTitle()?>.

<?php
    unset($this->vars['notification']);
