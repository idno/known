<div class="row">
    
    <div class="col-md-10 col-md-offset-1">
        <h1>
            <?php echo \Idno\Core\Idno::site()->language()->_('Following...'); ?>
        </h1>
        <?php echo $this->draw('account/menu') ?>
    
    <div class="explanation">
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_('Manage your subscriptions here. These users can also be given access to private content you produce on your site.'); ?>
            </p>
        </div>
    
    <div class="well">
        <p><?php echo \Idno\Core\Idno::site()->language()->_('Use this bookmarklet to make it easy to add new friends.'); ?></p>
    
        <?php echo $this->draw('account/settings/following/bookmarklet'); ?>
    </div>
    
    </div>
</div>
