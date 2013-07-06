<div >
    <h2 class="p-name">
        <a href="<?=$vars['object']->getURL()?>" class="u-url"><?=$vars['object']->getTitle()?></a>
    </h2>
    <div class="well">
        <p class="p-summary">
            <?=$vars['object']->summary?>
        </p>
        <p>
            Location: <span class="p-location"><?=$vars['object']->location?></span>
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

    <?php echo $this->autop($this->parseHashtags($vars['object']->body)); //TODO: a better rendering algorithm ?></div>