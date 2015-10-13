<?php

    $object = $vars['object'];
    /*
     * @var \Idno\Common\Entity $object
     */

?>

    <div class="">

        <h2 class="p-geo">
            <?php
                if (empty($vars['feed_view'])) {
                    ?>
                    <a href="<?= $object->getURL() ?>" class="p-name"><?= $object->getTitle() ?></a>
                    <?php

                }

            ?>
            <span class="h-geo">
            <data class="p-latitude" value="<?= $object->lat ?>"></data>
            <data class="p-longitude" value="<?= $object->long ?>"></data>
        </span>
        </h2>
        <div class="p-map">
            <?php
                if (!empty($object->body)) {
                    echo $this->autop($this->parseURLs($this->parseHashtags($object->body)));
                }

                if (!empty($object->tags)) {
                    ?>

                    <p class="tag-row"><i class="icon-tag"></i> <?= $this->parseHashtags($object->tags) ?></p>

                <?php } ?>
        </div>

    </div>
