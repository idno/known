<?php

$client = $vars['object'];

?>

<form action="<?= $client ? $client->getEditUrl() : \Idno\Core\site()->config()->getDisplayURL() . 'admin/oauth2client/'; ?>" class="form-horizontal" method="post" enctype="multipart/form-data">
    
    <?php if ($client) { ?>
    <input type="hidden" class="form-control" required name="id" value="<?= $client->_id?>" >
    
    <div class="well button" style="margin-top:20px;">
	<?= $client->draw() ?>
    </div>
    
    <div class="well details">
	<div class="row">
	    <div class="col-md-3">
		<p>
		    <label class="control-label" for="label"><?= \Idno\Core\Idno::site()->language()->_('Your Callback URL'); ?></label>
		</p>
	    </div>
	    <div class="col-md-9">
		<p><label class="control-label"><?= $client->getUrl(); ?></label></p>
	    </div>
	</div>
    </div>
    
    <?php } ?>
    
    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="label"><?= \Idno\Core\Idno::site()->language()->_('Client Label'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="text" class="form-control" required name="label" value="<?= htmlspecialchars($client->label)?>" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Label for OAuth 2 client'); ?></p>
	</div>
    </div>

    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="signin_button"><?= \Idno\Core\Idno::site()->language()->_('"Sign in with" button'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="file" class="form-control" name="signin_button">
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Graphic to use for the sign in with function'); ?></p>
	</div>
    </div>


    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="client_id"><?= \Idno\Core\Idno::site()->language()->_('Client ID'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="text" class="form-control" required name="client_id" value="<?= htmlspecialchars($client->client_id)?>" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Public key from your OAuth server'); ?></p>
	</div>
    </div>

    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="client_secret"><?= \Idno\Core\Idno::site()->language()->_('Secret key'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="text" class="form-control" required name="client_secret" value="<?= htmlspecialchars($client->client_secret)?>" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Secret key from your OAuth server'); ?></p>
	</div>
    </div>

    <!-- <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="redirect_uri"><?= \Idno\Core\Idno::site()->language()->_('Redirect URI'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="url" class="form-control" required name="redirect_uri" value="<?= htmlspecialchars($client->redirect_uri)?>" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Where should we send the visitor after the OAuth2 handshake?'); ?></p>
	</div>
    </div> -->

    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="url_authorise"><?= \Idno\Core\Idno::site()->language()->_('Authorise URL'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="url" class="form-control" required name="url_authorise" value="<?= htmlspecialchars($client->url_authorise)?>" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Where should we send the auth request.'); ?></p>
	</div>
    </div>

    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="url_access_token"><?= \Idno\Core\Idno::site()->language()->_('Access Token URL'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="url" class="form-control" required name="url_access_token" value="<?= htmlspecialchars($client->url_access_token)?>" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Access token URL.'); ?></p>
	</div>
    </div>
    
    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="url_resource"><?= \Idno\Core\Idno::site()->language()->_('Owner Resource URL'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="url" class="form-control" name="url_resource" value="<?= htmlspecialchars($client->url_resource)?>">
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Owner resource URL.'); ?></p>
	</div>
    </div>
    
    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="scopes"><?= \Idno\Core\Idno::site()->language()->_('Scopes (space separated)'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="text" class="form-control" name="scopes" value="<?= htmlspecialchars($client->scopes)?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Scopes (space separated), e.g. openid profile email'); ?></p>
	</div>
    </div>
    
    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="scopes"><?= \Idno\Core\Idno::site()->language()->_('Public Key location'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
	    <input type="url" class="form-control" name="publickey_url" value="<?= htmlspecialchars($client->publickey_url)?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" >
	</div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('If using OpenID Connect, the url of the public key'); ?></p>
	</div>
    </div>
    
    <div class="row">
	<div class="col-md-2">
	    <p>
		<label class="control-label" for="federation"><?= \Idno\Core\Idno::site()->language()->_('Federate with this server using OpenID Connect'); ?></label>
	    </p>
	</div>
	<div class="col-md-4">
            <input type="checkbox" data-toggle="toggle" data-onstyle="info"
                data-on="<?php echo \Idno\Core\Idno::site()->language()->_('Yes'); ?>"
                data-off="<?php echo \Idno\Core\Idno::site()->language()->_('No'); ?>"
                value="true" id="federation"
                name="federation" <?php if ($client->federation) echo 'checked'; ?>>
        </div>
	<div class="col-md-6">
	    <p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('If checked, users on this server will be able to make authenticated API requests to this site. Leave off unless you know what you\'re doing!'); ?></p>
	</div>
    </div>

    <div>

	<div class="controls-save">
	    <button type="submit" class="btn btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Save'); ?></button>
	</div>
    </div>

<?= \Idno\Core\site()->actions()->signForm('/admin/oauth2client/')?>
</form>