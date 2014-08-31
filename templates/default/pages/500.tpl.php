<div class="h-entry result-500">
    <div class="row" style="margin-bottom: 2em; margin-top: 4em">

        <div class="offset1 span10">
            <h1 class="p-name">
                <?php if (!empty($vars['exception'])) { ?>
                Ooops! <?= get_class($vars['exception']); ?>
                <?php } else { ?>
                Ooops!
                <?php } ?>
            </h1>
        </div>
    </div>
    <?php if (!empty($vars['exception'])) { ?>
    <div class="row">
        <div class="offset1 span10">
            <p class="p-summary"><?= $vars['exception']->getMessage(); ?></p>
            <?php if (($debug = \Idno\Core\site()->config()->debug) && (!empty($debug))) { ?>
            <pre>
            <?= $vars['exception']->getTraceAsString(); ?>
            </pre>
            <?php } ?>
        </div>
    </div>
    <?php } else { ?>
    <div class="row">
        <div class="offset1 span10">
            <p class="p-summary">Oh no! Known had a problem.</p>
        </div>
    </div>
    <?php } ?>
</div>