<div class="row">

    <div class="col-md-10 col-md-offset-1">
                <?php echo $this->draw('admin/menu')?>
        <h1 style="margin-top: 1em; margin-bottom: 1em; text-align: center"><?php echo \Idno\Core\Idno::site()->language()->_('About Known'); ?></h1>

    </div>

</div>
<div class="row" style="margin-top: 1em">
    <div class="col-md-1 col-md-offset-3 col-xs-3">
        <a href="https://withknown.com"><img src="<?php echo \Idno\Core\Idno::site()->config()->getStaticURL()?>gfx/logos/logo_k.png" style="width: 70px; border: 0"></a>
    </div>
    <div class="col-md-5">
        <p style="font-size: 1.6em"><a href="https://withknown.com/?utm_source=admin&utm_medium=installation"><?php echo \Idno\Core\Idno::site()->language()->_('Known'); ?></a> <?php echo \Idno\Core\Idno::site()->language()->_('is a social publishing platform for groups and individuals.'); ?></p>
        <p class="explanation">
            <a href="https://withknown.com/opensource/?utm_source=admin&utm_medium=installation"><?php echo \Idno\Core\Idno::site()->language()->_('Open source project details'); ?></a>
        </p>
        <p>
            <?php echo \Idno\Core\Idno::site()->language()->_('Version: %s+%s', [\Idno\Core\Version::version(), \Idno\Core\Version::build()]); ?>
        </p>
    </div>
</div>
<div class="row" style="margin-top: 1em">

    <center>
        <a href="https://opencollective.com/known/contribute" target="_blank" rel="noopener noreferrer">
            <img src="https://opencollective.com/known/contribute/button@2x.png?color=blue" width="300">
        </a>
    </center>
</div>
