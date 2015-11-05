<div id="form-main">
    <div id="form-div">
        <h2>Share to social</h2>

        <p class="social-connect">
            Connect your accounts to easily share content with people across the web.
        </p>

        <?= $this->draw('onboarding/connect/networks'); ?>

        <div class="col-md-12 next-bar" align="center">
            <button class="btn btn-primary btn-lg btn-responsive"
                    onclick="window.location = '<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>begin/publish'; return false;">
                Continue
            </button>
        </div>

        <p align="center">
            Don't worry, you can always connect these later.
        </p>

    </div>
</div>