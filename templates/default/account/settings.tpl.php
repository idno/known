<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="span10 offset1">
        <?= $this->draw('account/menu') ?>
        <h1>
            Settings
        </h1>

        <div class="explanation">
            <p>
                Change your basic account settings here. You may also want to <a
                    href="<?= \Idno\Core\site()->session()->currentUser()->getDisplayURL() ?>/edit/">edit your
                    profile</a>.
            </p>
        </div>
    </div>
</div>
 <div class="row">
    <div class="span10 offset1">       

        <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings" method="post" class="form-horizontal"
              enctype="multipart/form-data">
	              
	    <div class="row">          
            <div class="span2">
                <label class="control-label" for="inputName">Your name</label>
            </div>
            <div class="span4">
                    <?= $this->__(['id' => 'inputName', 'value' => $user->getTitle(), 'class' => 'span4', 'name' => 'name', 'placeholder' => 'Your name'])->draw('forms/input/text'); ?>
                </div>
                <div class="span2">
                </div>
            </div>
            
            <div class="row">            
            <div class="span2">
                <label class="control-label" for="inputHandle">Your username</label>
            </div>
            <div class="span4">
                    <?= $this->__(['id' => 'inputHandle', 'value' => $user->handle, 'class' => 'span4', 'name' => 'handle', 'placeholder' => 'Your username'])->draw('forms/input/text'); ?>
            </div>
            <div class="span2">
            </div>
           </div>
           
           <div class="row">
            <div class="span2">
                <label class="control-label" for="inputEmail">Your email address</label>
            </div>
			<div class="span4">
                    <?= $this->__(['id' => 'inputEmail', 'value' => $user->email, 'class' => 'span4', 'name' => 'email', 'placeholder' => 'Your email address'])->draw('forms/input/email'); ?>
            </div>
            <div class="span4 config-desc">
	            Site notifications will be sent here.
            </div>
           </div>
           
           <div class="row">
            <div class="span2">
                <label class="control-label" for="inputPassword">Your password<br/>
                </label>
            </div>
            <div class="span4">
                    <?= $this->__(['id' => 'inputPassword', 'class' => 'span4', 'name' => 'password', 'placeholder' => 'Password', 'autocomplete' => 'off', 'value' => ''])->draw('forms/input/password'); ?> 
             </div>
            <div class="span4 config-desc">
	            Leave this blank if you don't want to change it.
	        </div>
           </div>
            <div class="control-group">
                <div class="controls-save">
                    <button type="submit" class="btn btn-primary">Save updates</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/account/settings') ?>

        </form>
    </div>

</div>