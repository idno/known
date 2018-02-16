<div class="row">
    <div class="col-lg-10 col-lg-offset-1">

        <h1<?= \Idno\Core\Idno::site()->language()->_('Notifications'); ?></h1>
        <p class="explanation">
            <?= \Idno\Core\Idno::site()->language()->_('The notifications screen shows you who has interacted with your content.'); ?>
        </p>

    </div>
</div>

<?php
    if (!empty($items)) {
        foreach ($items as $notif) {
            echo $t->__(['notification' => $notif])->draw($notif->getMessageTemplate());
        }
    } else {

?>

<div class="row">
    <div class="col-lg-10 col-lg-offset-1">

        <p>
            <?= \Idno\Core\Idno::site()->language()->_("You don't have any notifications yet. But we love you anyway!"); ?>
        </p>
        <p style="text-align: center">
            <img src="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/robots/feedback.png">
        </p>

    </div>
</div>

<?php

    }

    echo $t->drawPagination($vars['count'], $vars['items_per_page']);
?>

<script>
    $(function () {
        console.log($(".notification time"));
        $(".notification time").timeago();
                
        if (!Notifications.isEnabled()) { Notifications.enable(); } // Only enable (and prompt for enable) on this page.
    });
</script>