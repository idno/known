<?php

if (!empty($vars['messages'])) {
    $messages = $vars['messages'];

    ?>
        <div class="alerts">
        <?php

        foreach ($messages as $message) {

            ?>

                    <div class="alert <?php echo $message['message_type'] ?>">
                <?php echo $this->autop($message['message']) ?>
                    </div>

                <?php

        }

        ?>
        </div>
    <?php

}


