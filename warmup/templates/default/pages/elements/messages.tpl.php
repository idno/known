<?php

if (!empty($vars['messages'])) {

    ?>
    <div <?php if (!empty($vars['style'])) { echo 'style="' . $vars['style'] . '"'; } ?> <?php if (!empty($vars['id'])) { echo 'id="' . $vars['id'] . '"'; } ?> class="alerts">
        <div class="alert">
            <?=$messages?>
        </div>
    </div>
    <?php

}