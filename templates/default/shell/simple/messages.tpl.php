<?php

    if (!empty($vars['messages'])) {
        $messages = $vars['messages'];

        ?>
        <div class="alerts">
            <?php

                foreach ($messages as $message) {

                    ?>

                    <div class="alert <?= $message['message_type'] ?>">
                        <?= $this->autop($message['message']) ?>
                    </div>

                <?php

                }

            ?>
        </div>
    <?php

    }


