<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$target       = $notification->getTarget();
?>

<?php if (isset($annotation['owner_image'])) { ?>
    <a href="<?php echo $annotation['owner_url']?>">
        <img src="<?php echo $annotation['owner_image']?>" style="width: 100px; margin-left: 10px; margin-bottom: 10px" align="right">
    </a>
<?php } ?>

Hi! We wanted to let you know that <strong><a href="<?php echo $annotation['owner_url']?>"><?php echo $annotation['owner_name']?></a></strong>
mentioned you on <a href="<?php echo $annotation['permalink'];?>"><?php echo $annotation['permalink'];?></a>

<?php
    unset($this->vars['notification']);
