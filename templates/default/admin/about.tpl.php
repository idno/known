<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu')?>
        <h1 style="margin-top: 1em; margin-bottom: 1em; text-align: center">About Known</h1>

    </div>

</div>
<div class="row" style="margin-top: 1em">
    <div class="col-md-1 col-md-offset-3 col-xs-3">
        <a href="https://withknown.com"><img src="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>gfx/logos/logo_k.png" style="width: 70px; border: 0"></a>
    </div>
    <div class="col-md-5">
        <p style="font-size: 1.6em"><a href="https://withknown.com/?utm_source=admin&utm_medium=installation">Known</a> is a social publishing platform for groups and individuals.</p>
        <p class="explanation">
            <a href="https://withknown.com/services/?utm_source=admin&utm_medium=installation">Known Services</a> are available.
            You can also <a href="https://withknown.com/opensource/?utm_source=admin&utm_medium=installation">learn more about our open source project.</a>
        </p>
        <p>
            Version: <?= \Idno\Core\Idno::site()->version(); ?>
        </p>
    </div>
</div>
<div class="row" style="margin-top: 1em">

    <div class="col-md-2">
        &nbsp;
    </div>
</div>
