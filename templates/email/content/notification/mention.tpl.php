<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$target       = $notification->getTarget();
?>

<?php if (isset($annotation['owner_image'])) { ?>
    <a href="<?=$annotation['owner_url']?>">
        <img src="<?=$annotation['owner_image']?>" style="width: 100px; margin-left: 10px; margin-bottom: 10px" align="right">
    </a>
<?php } ?>

Hi! We wanted to let you know that <strong><a href="<?=$annotation['owner_url']?>"><?=$annotation['owner_name']?></a></strong>
mentioned you on <a href="<?=$annotation['permalink'];?>"><?=$annotation['permalink'];?></a>
