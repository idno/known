<?php

if (!empty($vars['items'])) {

    foreach($vars['items'] as $item) {

        /* @var \Idno\Entities\Reader\FeedItem $item */
        ?>

        <?php echo $item->draw()?>

        <?php

    }

}

