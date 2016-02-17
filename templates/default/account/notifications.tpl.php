<div class="row">
    <div class="col-lg-10 col-lg-offset-1">

        <h1>Notifications</h1>
        <p class="explanation">
            The notifications screen shows you who has interacted with your content.
        </p>

    </div>
</div>

<?php
    if (!empty($notifications)) {
        foreach ($notifications as $notif) {
            echo $t->__(['notification' => $notif])->draw($notif->getMessageTemplate());
        }
    } else {

?>

<div class="row">
    <div class="col-lg-10 col-lg-offset-1">

        <p>
            You don't have any notifications yet. But we love you anyway!
        </p>
        <p style="text-align: center">
            <img src="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/robots/feedback.png">
        </p>

    </div>
</div>

<?php

    }

    echo $t->drawPagination($vars['count']);
?>

<script>
    $(function () {
        console.log($(".notification time"));
        $(".notification time").timeago();
        enableNotifications();
    });
</script>