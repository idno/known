<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">
    
    <div class="span10 offset1">
	<h3>
	    Account settings
	</h3>
    <?=$this->draw('account/menu')?>
	<div class="hero-unit">
	    <p>
		Change your basic account settings here.
	    </p>
	    <form action="/account/settings" method="post" class="form-horizontal" enctype="multipart/form-data">
            <div class="control-group">
                <label class="control-label" for="inputName">Your name</label>
                <div class="controls">
                    <input type="text" id="inputName" placeholder="Your name" class="span4" name="name" value="<?=htmlspecialchars($user->getTitle())?>" >
                </div>
            </div>
		<div class="control-group">
		    <label class="control-label" for="inputHandle">Your handle</label>
		    <div class="controls">
			<input type="text" id="inputHandle" placeholder="Your handle" class="span4" name="handle" value="<?=htmlspecialchars($user->handle)?>" disabled>
		    </div>
		</div>
		<div class="control-group">
		    <label class="control-label" for="inputEmail">Your email address</label>
		    <div class="controls">
			<input type="email" id="inputEmail" placeholder="Your email address" class="span4" name="email" value="<?=htmlspecialchars($user->email)?>">
		    </div>
		</div>
		<div class="control-group">
		    <label class="control-label" for="inputPassword">Your password<br /><small>Leave this blank if you don't want to change it</small></label>
		    <div class="controls">
			<input type="password" id="inputPassword" placeholder="Password" class="span4" name="password" >
		    </div>
		</div>
		<div class="control-group">
		    <label class="control-label" for="inputPassword2">Your password again</label>
		    <div class="controls">
			<input type="password" id="inputPassword2" placeholder="Your password again" class="span4" name="password2">
		    </div>
		</div>
        <div class="control-group">
            <label class="control-label" for="inputAvatar">Upload a new avatar<br /><small>This is here temporarily.</small></label>
            <div class="controls">
                <input type="file" id="inputAvatar" class="span4" name="avatar">
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