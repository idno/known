<div class="row">

    <div class="span10 offset1">
        <h3>
            Create an account
        </h3>
        <div class="hero-unit">
            <p>

            </p>
            <form class="secure-form" action="<?=\Idno\Core\site()->config()->url?>account/register" method="post" class="form-horizontal">
		
		<div class="non-ssl-warning alert alert-danger" <?php if (\Idno\Core\site()->currentPage->isSSL()) { ?>style="display: none;"<?php } ?>>
		    <h4>Warning: Page not secure!</h4>
		    <p>It looks like this page is not secure, this means that your username and password can be easily read by GCHQ, the NSA, and other criminals. </p>
		    <p>It is <strong>STRONGLY</strong> recommended that you ask your administrator to configure TLS support on your web server before proceeding!</p>
		</div>
		
                <div class="control-group">
                    <label class="control-label" for="inputUsername">Your name<br />
                    <small>The name other people will see.</small></label>
                    <div class="controls">
                        <input type="text" id="inputName" placeholder="Your name" class="span4" name="name" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputUsername">Your handle<br />
                        <small>A one-word name that will be part of your profile URL.</small>
                    </label>
                    <div class="controls">
                        <input type="text" id="inputUsername" placeholder="Your username" class="span4" name="handle" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail">Your email address</label>
                    <div class="controls">
                        <input type="email" id="inputEmail" placeholder="Your email address" class="span4" name="email" value="">
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
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
                <?= \Idno\Core\site()->actions()->signForm('/account/register') ?>

            </form>
        </div>
    </div>

</div>