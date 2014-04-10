<div class="row">
    <div class="span6 offset3 well text-center">

        <h3 class="text-center">
            Sign in
        </h3>

        <form class="secure-form" action="<?= \Idno\Core\site()->config()->url ?>session/login" method="post">
	    <div class="non-ssl-warning alert alert-danger" <?php if (\Idno\Core\site()->currentPage->isSSL()) { ?>style="display: none;"<?php } ?>>
		<h4>Warning: Page not secure!</h4>
		<p>It looks like your login page is not secure, this means that your username and password can be easily read by GCHQ, the NSA, and other criminals. </p>
		<p>It is <strong>STRONGLY</strong> recommended that you ask your administrator to configure TLS support on your web server before proceeding!</p>
	    </div>
	    
            <div class="control-group">
                <div class="controls">
                    <input type="text" id="inputEmail" name="email" placeholder="Your username or email address"
                           class="span4">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input type="password" id="inputPassword" name="password" placeholder="Password" class="span4">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn">Sign in</button>
                    <input type="hidden" name="fwd" value="<?php
                        if (!empty($vars['fwd'])) {
                            echo htmlspecialchars($vars['fwd']);
                        } else if (!empty($_SERVER['HTTP_REFERER'])) {
                            echo htmlspecialchars($_SERVER['HTTP_REFERER']);
                        } else {
                            echo \Idno\Core\site()->config()->url;
                        }?>" />
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/session/login') ?>
        </form>
	
    </div>
</div>