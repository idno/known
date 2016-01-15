<div class="h-entry result-500">
    <div class="row" style="margin-bottom: 2em; margin-top: 4em">

        <div class="col-md-offset-1 col-md-10">
            <h1 class="p-name">
                Oh no! Something went wrong.
                <?php /*
                <?php if (!empty($vars['exception'])) { ?>
                Ooops! <?= get_class($vars['exception']); ?>
                <?php } else { ?>
                Oh no! Something went wrong.
                <?php } ?> */ ?>
            </h1>
        </div>
    </div>
    <?php if (!empty($vars['exception'])) { ?>
    <div class="row">
        <div class="col-md-offset-1 col-md-10">
            <p class="p-summary"><?= $vars['exception']->getMessage(); ?></p>
            <p>
                <a href="#" onclick="window.history.back();">Click here to go back and try again.</a>
            </p>
            <?php if (($debug = \Idno\Core\Idno::site()->config()->debug) && (!empty($debug))) { ?>
                <p>
                    <small><a href="#" onclick="$('#details').show(); return false;">Click here to see the technical details.</a></small>
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
            <p class="p-summary">Oh no! Something went wrong.</p>
        </div>
    </div>
    <?php } ?>
</div>