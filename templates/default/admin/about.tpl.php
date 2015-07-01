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
    <div class="col-md-9 col-xs-9">
        <p style="font-size: 1.6em"><a href="https://withknown.com/?utm_source=admin&utm_medium=installation">Known</a> is a publishing platform for everyone.</p>
        <p>
            Version: <?= \Idno\Core\site()->version(); ?>
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
