<div class="row">
    
    <div class="col-md-10 col-md-offset-1">
        <h1>
            <?= \Idno\Core\Idno::site()->language()->_('Following...'); ?>
        </h1>
        <?= $this->draw('account/menu') ?>
	
	<div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('Manage your subscriptions here. These users can also be given access to private content you produce on your site. '); ?>
            </p>
        </div>
	
	<div class="well">
	    <p><?= \Idno\Core\Idno::site()->language()->_('Use this bookmarklet to make is easy to add new friends.'); ?></p>
	
	    <?= $this->draw('account/settings/following/bookmarklet'); ?>
	</div>
	
    </div>
</div>