Hi! We wanted to let you know that *<?=$vars['owner_name']?>* replied to the post *<?=$vars['object']->getNotificationTitle()?>*.

Here's what they said:

> <?= strip_tags(preg_replace('#<br\s*/?>#i', "\n> ", str_replace("\n", "\n> ", $vars['content']))); ?>

<?php

    if (!empty($vars['object'])) {

?>

    View post: <?=$vars['object']->getDisplayURL()?>
<?php

    }

?>