<div class="row">
    <div class="span6 offset3 well text-center">

		<h2 class="text-center welcome">Welcome back!</h2>

        <h3 class="text-center">
            Sign in
        </h3>

        <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>session/login" method="post">
            <div class="control-group">
                <div class="controls">
                    <input type="text" id="inputEmail" name="email" placeholder="Your email address or username"
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
                    <button type="submit" class="btn btn-signin">Sign in</button>
                    <input type="hidden" name="fwd" value="<?php
                        if (!empty($vars['fwd'])) {
                            echo htmlspecialchars($vars['fwd']);
                        } else if (!empty($_SERVER['HTTP_REFERER'])) {
                            echo htmlspecialchars($_SERVER['HTTP_REFERER']);
                        } else {
                            echo \Idno\Core\site()->config()->getDisplayURL();
                        }?>" />
                </div>
            </div>
            
              <div class="control-group">
                <div class="controls">
                    <?php
                        if (\Idno\Core\site()->config()->open_registration == true && \Idno\Core\site()->config()->canAddUsers()) {
                    ?>
                    <a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>account/register">New here? Register for an account.</a><br><br>
                    <?php
                        }
                    ?>
                    <a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>account/password">Forgot your password?</a>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/session/login') ?>
        </form>

    </div>
</div>