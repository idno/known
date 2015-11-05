<?php

    if (\Idno\Core\Idno::site()->hub() || \Idno\Core\Idno::site()->session()->isAdmin()) {

?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <?= $this->draw('account/menu') ?>
        <div id="service-placeholder"></div>
        <iframe width="100%" src="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>withknown/settings" style="border: none; height: 2000px; overflow: hidden; margin-top: -3em;" scrolling="no" allowtransparency="true" title="Connect services with Convoy"></iframe>
    </div>
</div>
<?php

    } else {

?>
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <?= $this->draw('account/menu') ?>
                <h1>
                    Connect Services
                </h1>
                <p>
                    Services haven't been enabled on this site yet. Check back soon!
                </p>
            </div>
        </div>
<?php

    }
