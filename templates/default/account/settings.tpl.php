<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">
    
    <div class="span10 offset1">
	<h3>
	    Account settings
	</h3>
	<div class="hero-unit">
	    <p>
		Change your basic account settings here.
	    </p>
	    <form action="/account/settings" method="post" class="form-horizontal">
		<div class="control-group">
		    <label class="control-label" for="inputEmail">Your username</label>
		    <div class="controls">
			<input type="text" id="inputEmail" placeholder="Your username or email address" class="span4" name="handle" value="<?=htmlspecialchars($user->handle)?>" disabled>
		    </div>
		</div>
		<div class="control-group">
		    <label class="control-label" for="inputEmail">Your email address</label>
		    <div class="controls">
			<input type="email" id="inputEmail" placeholder="Your email address" class="span4" name="email" value="<?=htmlspecialchars($user->email)?>">
		    </div>
		</div>
		<div class="control-group">
		    <label class="control-label" for="inputEmail">Your password<br /><small>Leave this blank if you don't want to change it</small></label>
		    <div class="controls">
			<input type="password" id="inputPassword" placeholder="Password" class="span4" name="password" >
		    </div>
		</div>
		<div class="control-group">
		    <label class="control-label" for="inputEmail">Your password again</label>
		    <div class="controls">
			<input type="password" id="inputPassword" placeholder="Your password again" class="span4" name="password2">
		    </div>
		</div>
		<div class="control-group">
		    <div class="controls">
			<button type="submit" class="btn-primary">Save</button>
		    </div>
		</div>
		<?= \Idno\Core\site()->actions()->signForm('/account/settings') ?>

	    </form>
	</div>
    </div>
    
</div>