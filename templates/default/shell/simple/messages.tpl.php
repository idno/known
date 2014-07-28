<?php

    $messages = $vars['messages'];

    if (!empty($messages)) {

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


