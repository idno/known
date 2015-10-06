<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu')?>
        <h1>About Known</h1>

    </div>

</div>
<div class="row">
    <div class="col-md-1 col-md-offset-1 col-xs-3">
        <a href="https://withknown.com"><img src="<?=\Idno\Core\site()->config()->getDisplayURL()?>gfx/logos/logo_k.png" style="width: 70px; border: 0"></a>
    </div>
    <div class="col-md-5">
        <p style="font-size: 1.6em"><a href="https://withknown.com/?utm_source=admin&utm_medium=installation">Known</a> is a publishing platform for everyone.</p>
        <p>
            Version: <?= \Idno\Core\site()->version(); ?>
        </p>
    </div>
    <div class="col-md-4">
        <p>
            Exclusive Web Hosting Sponsor:
        </p>
        <p>
            <a href="https://dreamhost.com/redir.cgi?promo=known595&url=promo/known595/&utm_source=known&utm_medium=banner&utm_content=panelshared595&utm_campaign=shared"></a><img src="https://withknown.com/img/sponsor_dh_long.png" style="width: 100%"></a>
        </p>
        <p>
            Known is compatible with <a href="https://dreamhost.com/redir.cgi?promo=known595&url=promo/known595/&utm_source=known&utm_medium=banner&utm_content=panelshared595&utm_campaign=shared">DreamHost's unlimited hosting plan</a>.
        </p>
    </div>
</div>
<div class="row" style="margin-top: 1em">
    <div class="col-md-8 col-md-offset-1">
        <div style="background-color: #fff; color: #000; font-family: monospace; font-size: 0.9em; padding: 2em">
            <?php

                $contributors = file_get_contents(\Idno\Core\site()->config()->path . '/CONTRIBUTORS.md');
                echo $this->autop($this->parseURLs($contributors));

            ?>
        </div>
    </div>
    <div class="col-md-2">
        &nbsp;
    </div>
</div>
