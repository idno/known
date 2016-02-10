
<?php foreach ($notifications as $notif) { ?>

    <div class="panel panel-default notification">
        <div class="panel-heading">
            <?php if (!$notif->isRead()) { ?>
                <i class="fa fa-circle"></i>
            <?php } ?>

            <?= $notif->getMessage() ?>

        </div>
        <div class="panel-body">

            <?= $t->__(['notification' => $notif])->draw($notif->getMessageTemplate()); ?>

            <form action="<?= $notif->getURL() ?>" method="POST">
                <input type="hidden" name="read"  value="true">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/account/notifications') ?>
            </form>

        </div>
    </div>

<?php } ?>
