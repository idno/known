<div class="row">

    <div class="col-md-8 col-md-offset-2">
        <h3 class="register">
            <?php echo \Idno\Core\Idno::site()->language()->_('Hello there!'); ?>
        </h3>
        <h4 class="register"><?php echo \Idno\Core\Idno::site()->language()->_('Create a new account to get started.'); ?></h4>
        <div class="jumbotron">
            <p>

            </p>
            <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/register" method="post" style="width: 100%" class="form-horizontal">
                <div class="control-group">
                   <label class="control-label" for="inputUsername"><?php echo \Idno\Core\Idno::site()->language()->_('Your name'); ?></label>
                    <div class="controls">
                        <input type="text" id="inputName" placeholder="Henri Matisse" class="" style="width: 100%" name="name" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputUsername"><?php echo \Idno\Core\Idno::site()->language()->_('Choose a handle'); ?>
                        <br /><small><?php echo \Idno\Core\Idno::site()->language()->_('Handles identify you throughout the site. Something simple is best: for example, <em>janedoe</em>.'); ?></small></label>
                    <div class="controls">
                        <input type="text" id="inputUsername" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('username'); ?>" class="" style="width: 100%" name="handle" value="" autocapitalize="off" autocorrect="off" spellcheck="false">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?php echo \Idno\Core\Idno::site()->language()->_('Your email address'); ?></label>
                    <div class="controls">
                        <input type="email" id="inputEmail" placeholder="you@email.com" class="" style="width: 100%" name="email" value="<?php echo htmlentities($vars['email'])?>" autocapitalize="off">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?php echo \Idno\Core\Idno::site()->language()->_('Create a password'); ?>
                        <br /><small><?php echo \Idno\Core\Idno::site()->language()->_('At least 7 characters please.'); ?></small></label>
                    
                    <div class="controls">
                        <input type="password" id="inputPassword" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('secret-password'); ?>" class="" style="width: 100%" name="password" >
                    </div>
                    
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?php echo \Idno\Core\Idno::site()->language()->_('Your password again'); ?></label>
                    <div class="controls">
                        <input type="password" id="inputPassword" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('secret-password'); ?>" class="" style="width: 100%" name="password2">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-reg"><?php echo \Idno\Core\Idno::site()->language()->_('Create Account'); ?></button>
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($vars['code'])?>">
                    </div>
                </div>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/account/register') ?>

            </form>
        </div>
    </div>

</div>