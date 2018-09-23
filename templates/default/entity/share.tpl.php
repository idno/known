<?php

if (!empty($vars['content_type'])) {

} else {

    ?>
        <p>
        <?php echo \Idno\Core\Idno::site()->language()->_("This content can't be shared right now."); ?>
        </p>
    <?php

}