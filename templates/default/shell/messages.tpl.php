<div id="page-messages">
    <?php

        if (!empty($vars['messages'])) {
            foreach ($vars['messages'] as $message) {

                ?>

                <div class="alert <?= $message['message_type'] ?> col-md-10 col-md-offset-1">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $message['message'] ?>
                </div>

                <?php

            }
        }

    ?>
</div>