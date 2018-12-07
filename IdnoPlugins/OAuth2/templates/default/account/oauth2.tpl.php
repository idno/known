<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('account/menu') ?>
        <h3><?= \Idno\Core\Idno::site()->language()->_('Manage OAuth2 Applications'); ?></h3>
	
    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

	<div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('These are your OAuth2 Applications, which you or others can use to connect third party applications to.'); ?>
            </p>
        </div>


        <form action="/account/oauth2/" class="form-horizontal" method="post">
	    <input type="hidden" name="action" value="create" />

	    <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputName"><?= \Idno\Core\Idno::site()->language()->_('Your application name'); ?></label>
                </div>

		<div class="col-md-4">
		    <input type="text" id="inputName" placeholder="<?= \Idno\Core\Idno::site()->language()->_('New Application name'); ?>" name="name"
			   value="" required>
                </div>
                <div class="col-md-5">
		    <button type="submit" class="btn btn-primary btn-large"><?= \Idno\Core\Idno::site()->language()->_('Generate new keys...'); ?></button>
		</div>

	    </div>

	    <?= \Idno\Core\site()->actions()->signForm('/account/oauth2/') ?>
        </form>
    </div>    
</div>

<div class="row">
    
    <div class="col-md-10 col-md-offset-1">
	
	<h3><?= \Idno\Core\Idno::site()->language()->_('Your Applications'); ?></h3>
	
    </div>
    <div class="pane col-md-8 col-md-offset-1">
	

	<?php
	if (!empty($vars['applications']) && is_array($vars['applications'])) {
	    foreach ($vars['applications'] as $app) {
		if ($app instanceof \IdnoPlugins\OAuth2\Application) {
		    ?>

	    	<div class="row">

	    	    <div class="col-md-2">
	    		<p>
			    <strong><?= $app->getTitle(); ?></strong>
	    		</p>
	    	    </div>
	    	    <div class="col-md-5">
	    		<p>
	    		    <small><strong><?= \Idno\Core\Idno::site()->language()->_('App Key'); ?>: </strong> <?= $app->key; ?></small>
	    		</p>
			<p>
	    		    <small><strong><?= \Idno\Core\Idno::site()->language()->_('Secret'); ?>: </strong> <?= $app->secret; ?></small>
	    		</p>
	    	    </div>
		    
	    	    <div class="col-md-1">
	    		<p><small>
				    <?php
				    if ($app->canEdit()) {
					echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'account/oauth2', \Idno\Core\Idno::site()->language()->_('Delete'), array('app_uuid' => $app->getUUID(), 'action' => 'delete'), array('class' => '', 'confirm' => true, 'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure? This will delete this application.')));
				    } else {
					echo '&nbsp';
				    }
				    ?>
	    		    </small></p>
	    	    </div>
		    
	    	</div>
		    <?php
		}
	    }
	}
	?>

    </div>

</div>