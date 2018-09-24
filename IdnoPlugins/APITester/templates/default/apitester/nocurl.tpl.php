<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <h1><?php echo \Idno\Core\Idno::site()->language()->_('API Tester'); ?></h1>
        <?php echo $this->draw('admin/menu')?>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="explanation">
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_('The API Tester requires the'); ?> <a href="http://php.net/curl"><?php echo \Idno\Core\Idno::site()->language()->_('curl'); ?></a> <?php echo \Idno\Core\Idno::site()->language()->_('extension'); ?>.
                <?php echo \Idno\Core\Idno::site()->language()->_('See the'); ?> <a href="<?php echo \Idno\Core\Idno::site()->config()->url?>admin/dependencies/"><?php echo \Idno\Core\Idno::site()->language()->_('dependencies page'); ?></a>
                <?php echo \Idno\Core\Idno::site()->language()->_('for more information about system dependencies.'); ?>
            </p>
        </div>
    </div>
</div>