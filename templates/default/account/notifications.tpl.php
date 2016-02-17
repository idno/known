
<?php
foreach ($notifications as $notif) {
    echo $t->__(['notification' => $notif])->draw($notif->getMessageTemplate());
}
echo $t->drawPagination($vars['count']);
?>



    <script>
    $(function() {
        console.log($(".notification time"));
        $(".notification time").timeago();
    });
    </script>