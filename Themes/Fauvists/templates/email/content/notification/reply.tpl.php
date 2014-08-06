<a href="<?=$vars['owner_url']?>"><img src="<?=$vars['owner_image']?>" style="width: 100px; margin-left: 10px; margin-bottom: 10px" align="right"></a>Hi! We wanted to let you know that <strong><a href="<?=$vars['owner_url']?>"><?=$vars['owner_name']?></a></strong> replied to your post <strong><a href="<?=$vars['object']->getURL();?>"><?=$vars['object']->getNotificationTitle()?></a></strong>.<br>
<br>
Here's the reply:<br>
<br>
<blockquote>
    <?=$vars['content']?>
</blockquote>
<br clear="all"><br>
<?php

    if (!empty($vars['object'])) {

?>
<div class="center">
    <a href="<?=$vars['object']->getURL()?>" style="background-color:#FFF;border:2px solid #028;border-radius:4px;color:#028;display:inline-block;font-family:sans-serif;font-size:17px;font-weight:normal;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;mso-hide:all;">View post</a>
</div>
<?php

    }

?>