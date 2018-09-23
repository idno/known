<div id="form-main">
    <div id="form-div">
        <h2><?php echo \Idno\Core\Idno::site()->language()->_('Share to social'); ?></h2>

        <p class="social-connect">
            <?php echo \Idno\Core\Idno::site()->language()->_('Connect your accounts to easily share content with people across the web.'); ?>
        </p>

        <?php echo $this->draw('onboarding/connect/networks'); ?>

        <div class="col-md-12 next-bar" align="center">
            <button class="btn btn-primary btn-lg btn-responsive"
                    onclick="window.location = '<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>begin/publish'; return false;">
                <?php echo \Idno\Core\Idno::site()->language()->_('Continue'); ?>
            </button>
        </div>

        <p align="center">
            <?php echo \Idno\Core\Idno::site()->language()->_("Don't worry, you can always connect these later."); ?>
        </p>

    </div>
</div>