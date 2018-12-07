<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <h3>Manage OAuth2 Applications</h3>
	<?= $this->draw('account/menu') ?>
    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

	<div class="explanation">
            <p>
                These are your OAuth2 Applications, which you or others can use to connect third party applications to.
            </p>
        </div>


        <form action="/account/oauth2/" class="form-horizontal" method="post">
	    <input type="hidden" name="action" value="create" />

	    <div class="control-group">
		<label class="control-label" for="inputName">Your application name</label>

		<div class="controls">
		    <input type="text" id="inputName" placeholder="New Application name" name="name"
			   value="" required>
		    
		    <button type="submit" class="btn btn-primary btn-large">Generate new keys...</button>
		</div>

	    </div>

	    <?= \Idno\Core\site()->actions()->signForm('/account/oauth2/') ?>
        </form>
    </div>    
</div>

<div class="row">
    
    <div class="col-md-10 col-md-offset-1">
	
	<h3>Your Applications</h3>
	
    </div>
    <div class="pane col-md-10 col-md-offset-1">
	

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
	    		    <small><strong>App Key: </strong> <?= $app->key; ?></small>
	    		</p>
			<p>
	    		    <small><strong>Secret: </strong> <?= $app->secret; ?></small>
	    		</p>
	    	    </div>
		    
	    	    <div class="col-md-1">
	    		<p><small>
				    <?php
				    if ($app->canEdit()) {
					echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'account/oauth2', 'Delete', array('app_uuid' => $app->getUUID(), 'action' => 'delete'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? This will delete this application.'));
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