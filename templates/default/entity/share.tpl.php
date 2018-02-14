<?php

    if (!empty($vars['content_type'])) {

    } else {

        ?>
        <p>
            <?= \Idno\Core\Idno::site()->language()->_("This content can't be shared right now."); ?>
        </p>
    <?php

    }