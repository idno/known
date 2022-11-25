<div class="row">

    <div class="col-md-8 col-md-offset-2">
        <div class="jumbotron">
                <h3 class="register">
            <?= \Idno\Core\Idno::site()->language()->_('Hello there!'); ?>
        </h3>
        <h4 class="register"><?= \Idno\Core\Idno::site()->language()->_('Create a new account to get started.'); ?></h4>
            <p>

            </p>
            <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/register" method="post" style="width: 100%" class="form-horizontal">
                <div class="control-group">
                   <label class="control-label" for="inputUsername"><?= \Idno\Core\Idno::site()->language()->_('Your name'); ?></label>
                    <div class="controls">
                        <input type="text" id="inputName" placeholder="Henri Matisse" class="" style="width: 100%" name="name" value="">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputUsername"><?= \Idno\Core\Idno::site()->language()->_('Choose a username'); ?></label>
                    <div class="controls">
                        <input type="text" id="inputUsername" placeholder="username" class="" style="width: 100%" name="handle" value="" autocapitalize="off">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?= \Idno\Core\Idno::site()->language()->_('Your email address'); ?></label>
                    <div class="controls">
                        <input type="email" id="inputEmail" placeholder="you@email.com" class="" style="width: 100%" name="email" value="<?php echo htmlentities($vars['email'])?>" autocapitalize="off">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?= \Idno\Core\Idno::site()->language()->_('Create a password'); ?></label>
                    
                    <div class="controls">
                        <input type="password" id="inputPassword" placeholder="secret-password" class="" style="width: 100%" name="password" >
                        <br /><small><?= \Idno\Core\Idno::site()->language()->_('(at least 7 characters please)'); ?></small>
                    </div>
                    
                </div>
                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?= \Idno\Core\Idno::site()->language()->_('Your password again'); ?></label>
                    <div class="controls">
                        <input type="password" id="inputPassword" placeholder="secret-password" class="" style="width: 100%" name="password2">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-reg"><?= \Idno\Core\Idno::site()->language()->_('Create Account'); ?></button>
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($vars['code'])?>">
                    </div>
                </div>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/account/register') ?>

            </form>
        </div>
    </div>

</div>