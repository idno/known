<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h1><?= \Idno\Core\Idno::site()->language()->_('API Tester'); ?></h1>
        <?=$this->draw('admin/menu')?>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('The API Tester requires the'); ?> <a href="http://php.net/curl"><?= \Idno\Core\Idno::site()->language()->_('curl'); ?></a> <?= \Idno\Core\Idno::site()->language()->_('extension'); ?>.
                <?= \Idno\Core\Idno::site()->language()->_('See the'); ?> <a href="<?=\Idno\Core\Idno::site()->config()->url?>admin/dependencies/"><?= \Idno\Core\Idno::site()->language()->_('dependencies page'); ?></a>
                <?= \Idno\Core\Idno::site()->language()->_('for more information about system dependencies.'); ?>
            </p>
        </div>
    </div>
</div>