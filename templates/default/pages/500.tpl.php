<div class="h-entry result-500">
    <div class="row" style="margin-bottom: 2em; margin-top: 4em">

        <div class="col-md-offset-1 col-md-10">
            <h1 class="p-name">
                <?= \Idno\Core\Idno::site()->language()->_('Something went wrong.'); ?>
            </h1>
        </div>
    </div>
    <?php if (!empty($vars['exception'])) { ?>
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <p class="p-summary"><?= $vars['exception']->getMessage(); ?></p>
            <p>
                <a href="#" onclick="window.history.back();"><?= \Idno\Core\Idno::site()->language()->_('Click here to try again'); ?>,</a> <?= \Idno\Core\Idno::site()->language()->_('or'); ?>
                <a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>"><?= \Idno\Core\Idno::site()->language()->_('click here to go back to the homepage'); ?></a>.
            </p>
            <?php if (($debug = \Idno\Core\Idno::site()->config()->debug) && (!empty($debug))) { ?>
                <p>
                    <small><a href="#" onclick="$('#details').show(); return false;"><?= \Idno\Core\Idno::site()->language()->_('Click here to see the technical details.'); ?></a></small>
                </p>
                <div id="details" style="display:none">
                    <pre>
<?= $vars['exception'] ?>
                    </pre>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php } else { ?>
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <p class="p-summary"><?= \Idno\Core\Idno::site()->language()->_('Oh no! Something went wrong.'); ?></p>
        </div>
    </div>
    <?php } ?>
</div>