<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <?php
            foreach ($notifications as $notif) {
                echo $t->__(['notification' => $notif])->draw($notif->getMessageTemplate());
            }
            echo $t->drawPagination($vars['count']);
        ?>
    </div>
</div>

<script>
    $(function () {
        console.log($(".notification time"));
        $(".notification time").timeago();
        enableNotifications();
    });
</script>