<?php

    $feed = $vars['object']->getFeedObject();
    if ($feed = $vars['object']->getFeedObject()) {

    ?>
    <div class="row subscription">
        <div class="span10 offset1">
            <p>
                <strong><a href="<?= $feed->url ?>"><?= $feed->getTitle() ?></a></strong><br>
                <span class="feed_url"><?= $feed->feed_url ?></span>
            </p>
        </div>
    </div>
<?php

    }