<?php
    if (empty($vars['feed_view'])) {

        ?>
        <h2 class="p-name">
            <a href="<?= $vars['object']->getDisplayURL() ?>"
               class="u-url"><?= htmlentities(strip_tags($vars['object']->getTitle()), ENT_QUOTES, 'UTF-8'); ?></a>
        </h2>
    <?php

    }
?>
<div class="well">
    <p class="p-summary">
        <?= htmlentities(strip_tags($vars['object']->summary), ENT_QUOTES, 'UTF-8');?>
    </p>
    <p>
        Location: <span class="p-location"><?= htmlentities(strip_tags($vars['object']->location), ENT_QUOTES, 'UTF-8'); ?></span>
    </p>
    <?php if (!empty($vars['object']->starttime)) { ?>
        <p>
            Time: <time class="dt-start" datetime="<?=date('c',strtotime($vars['object']->starttime))?>"><?=$vars['object']->starttime?></time>
        </p>
    <?php
    }
    ?>
    <?php if (!empty($vars['object']->endtime)) { ?>
        <p>
            Ends: <time class="dt-end" datetime="<?=date('c',strtotime($vars['object']->endtime))?>"><?=$vars['object']->endtime?></time>
        </p>
    <?php
    }
    ?>
</div>
<div class="e-content">
    <?php
        echo $this->autop($this->parseHashtags($this->parseURLs($vars['object']->body))); //TODO: a better rendering algorithm
    ?>
</div>