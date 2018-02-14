<div id="form-main">
    <div id="form-div">
        <h2><?= \Idno\Core\Idno::site()->language()->_('Share to social'); ?></h2>

        <p class="social-connect">
            <?= \Idno\Core\Idno::site()->language()->_('Connect your accounts to easily share content with people across the web.'); ?>
        </p>

        <?= $this->draw('onboarding/connect/networks'); ?>

        <div class="col-md-12 next-bar" align="center">
            <button class="btn btn-primary btn-lg btn-responsive"
                    onclick="window.location = '<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>begin/publish'; return false;">
                <?= \Idno\Core\Idno::site()->language()->_('Continue'); ?>
            </button>
        </div>

        <p align="center">
            <?= \Idno\Core\Idno::site()->language()->_("Don't worry, you can always connect these later."); ?>
        </p>

    </div>
</div>