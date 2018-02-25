<script>

    var data = {
        <?= $this->draw('account/firefox/manifest'); ?>
    }

    function activate(node) {
        var event = new CustomEvent("ActivateSocialFeature");
        node.setAttribute("data-service", JSON.stringify(data));
        node.dispatchEvent(event);
    }

</script>

<div class="row">

    <div class="col-md-10 col-md-offset-1" style="margin-top: 1em">
        <h2><?= \Idno\Core\Idno::site()->language()->_('Known for Firefox'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-md-offset-1">
        <p style="padding-bottom: 25px; padding-top: 15px;"><img
                src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>gfx/other/firefox-long.png" alt="firefox" class="img-responsive"/></p>

        <p><?= \Idno\Core\Idno::site()->language()->_('Adding Known to Firefox is the easiest way to bookmark links, share content, and reply to posts from any page on the web.'); ?></p>

        <p>
            <button class="firefox ff" onclick="activate(this)"><?= \Idno\Core\Idno::site()->language()->_('Activate now'); ?></button>
        </p>
        <p>
            <small><?= \Idno\Core\Idno::site()->language()->_('Requires'); ?> <strong><?= \Idno\Core\Idno::site()->language()->_('Firefox 21'); ?></strong> <?= \Idno\Core\Idno::site()->language()->_('or above. Download the latest version'); ?>
                <a href="http://www.mozilla.org/en-US/firefox/new/" target="_blank"><?= \Idno\Core\Idno::site()->language()->_('here'); ?></a>.
            </small>
        </p>
    </div>
    <div class="col-md-4 col-md-offset-1">
        <p style="text-align: right;">
            <img src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>gfx/other/firefoxsocial.png"
                 alt="firefoxsocial" class="img-responsive" />
        </p>
    </div>
</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <p><?= \Idno\Core\Idno::site()->language()->_('After clicking the'); ?> <strong><?= \Idno\Core\Idno::site()->language()->_('Activate'); ?></strong> <?= \Idno\Core\Idno::site()->language()->_('button, choose'); ?> <strong><?= \Idno\Core\Idno::site()->language()->_('Enable Services'); ?></strong> <?= \Idno\Core\Idno::site()->language()->_('from the browser message to install Known in your Firefox toolbar.'); ?></p>
    </div>
</div>
