<script>

    var data = {

        "name": "<?=htmlspecialchars(\Idno\Core\site()->config()->title)?>",
        "iconURL": "<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/logo_k_16.png",
        "icon32URL": "<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/logo_k_32.png",
        "icon64URL": "<?=\Idno\Core\site()->config()->getURL()?>gfx/logos/logo_k_64.png",

        "workerURL": "<?=\Idno\Core\site()->config()->url?>IdnoPlugins/Firefox/worker.js",
        //"sidebarURL": "<?=\Idno\Core\site()->config()->url?>firefox/sidebar",
        "shareURL": "<?=\Idno\Core\site()->config()->url?>share?share_url=%{url}&share_title=%{title}&via=ff_social",

        "description": "Powered by Known",
        "author": "Known, Inc",
        "homepageURL": "http://withknown.com/",

        "version": "0.1"
    }

    function activate(node) {
        var event = new CustomEvent("ActivateSocialFeature");
        node.setAttribute("data-service", JSON.stringify(data));
        node.dispatchEvent(event);
    }

</script>

<div class="row">

    <div class="span10 offset1">
        <h1>Firefox</h1>
        <?= $this->draw('account/menu') ?>
    </div>
</div>
<div class="row">
    <div class="span10 offset1" style="margin-top: 4em">

        <p>
            The very latest version of <a href="http://www.mozilla.org/en-US/firefox/new/" target="_blank">Mozilla
                Firefox</a>
            contains extensions that make it easy to post and keep up to date
            with <?= \Idno\Core\site()->config()->title ?>.
        </p>

        <p>
            If you have <strong>Firefox 21</strong> or above, click the button below to
            set up your browser:
        </p>

        <p>
            <button class="btn btn-success" onclick="activate(this)">Click here to begin using <?= \Idno\Core\site()->config()->title ?> for
                Firefox
            </button>
        </p>
        <p>
            If you don't have the required version of Firefox,
            <a href="http://www.mozilla.org/en-US/firefox/new/" target="_blank">click here to install it</a>.
        </p>

    </div>

</div>