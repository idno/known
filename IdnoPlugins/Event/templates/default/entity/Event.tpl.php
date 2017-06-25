<?php
if (empty($vars['feed_view'])) {

    ?>
    <h2 class="p-name">
        <a href="<?= $vars['object']->getDisplayURL() ?>"
           class="u-url"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
    </h2>
    <?php

}
$starttime = strtotime($vars['object']->starttime);
$endtime = strtotime($vars['object']->endtime);
$timeformat = 'l, jS F Y h:i A';
?>
<div class="well">
    <p class="p-summary">
        <strong><?= htmlentities(strip_tags($vars['object']->summary), ENT_QUOTES, 'UTF-8'); ?></strong>
    </p>
    <p>
        Location: <span
                class="p-location"><?= htmlentities(strip_tags($vars['object']->location), ENT_QUOTES, 'UTF-8'); ?></span>
    </p>
    <?php if (!empty($vars['object']->starttime)) { ?>
        <p>
            <time class="dt-start"
                  datetime="<?= date('c', $starttime) ?>"><?=date($timeformat, $starttime)?></time>
            <?php

            if ($endtime && $endtime < $starttime + 86400) {
                ?>- <time class="dt-end"
                  datetime="<?= date('c', $endtime) ?>"><?=date('h:i A', $endtime);?></time><?php
            }

            ?>
        </p>
        <?php
    }
    ?>
    <?php
        if ($endtime && $endtime >= $starttime + 86400) {
    ?>
        <p>
            Ends:
            <time class="dt-end"
                  datetime="<?= date('c', $endtime) ?>"><?= date($timeformat, $endtime) ?></time>
        </p>
        <?php
    }
    ?>
</div>
<div class="e-content">
    <?= $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body))) ?>
</div>
