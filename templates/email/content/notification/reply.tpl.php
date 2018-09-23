<?php
$notification = $vars['notification'];
$annotation   = $notification->getObject();
$post         = $notification->getTarget();
?>

<a href="<?php echo $annotation['owner_url']?>"><img src="<?php echo $annotation['owner_image']?>" style="width: 100px; margin-left: 10px; margin-bottom: 10px" align="right"></a>Hi! We wanted to let you know that <strong><a href="<?php echo $annotation['owner_url']?>"><?php echo $annotation['owner_name']?></a></strong> replied to the post <strong><a href="<?php echo $post->getDisplayURL();?>"><?php echo $post->getNotificationTitle()?></a></strong>.<br>
<br>
Here's what they said:<br>
<br>
<blockquote>
    <?php echo $annotation['content']?>
</blockquote>
<br class="clearall"><br>
<?php

if (!empty($post)) {

    ?>
<div class="center">
    <a href="<?php echo $post->getDisplayURL()?>" style="background-color:#73B2E3;border:1px solid #73B2E3;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:17px;font-weight:normal;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">View post</a>
</div>
    <?php

}

?>
<?php
    unset($this->vars['notification']);
