<div class="row">

    <div class="col-md-10 col-md-offset-1">

        <h1><?= \Idno\Core\Idno::site()->language()->_('Reset your password'); ?></h1>
        <p>
            <?= \Idno\Core\Idno::site()->language()->_('To change your password, enter your new password below:'); ?>
        </p>

    </div>
    <div class="col-md-10 col-md-offset-1">

        <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/password/reset" method="post" class="form-horizontal">

            <div class="control-group">
                <label class="control-label" for="inputEmail"><?= \Idno\Core\Idno::site()->language()->_('Your email address'); ?><br />
                    <small><?= \Idno\Core\Idno::site()->language()->_('The address associated with your %s account.', [\Idno\Core\Idno::site()->config()->title]); ?></small></label>
                <div class="controls">
                    <input type="email" id="inputEmail" placeholder="Email" class="form-control" name="email" value="<?=$vars['email']?>" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword"><?= \Idno\Core\Idno::site()->language()->_('New password (7 characters or more)'); ?></label>
                <div class="controls">
                    <input type="password" id="inputPassword" class="form-control" name="password" value="" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword2"><?= \Idno\Core\Idno::site()->language()->_('Retype password'); ?><br />
                    <small><?= \Idno\Core\Idno::site()->language()->_('We ask you to enter the password twice to protect against typos.'); ?></small></label>
                <div class="controls">
                    <input type="password" id="inputPassword2" class="form-control" name="password2" value="" required>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Reset password'); ?></button>
                    <input type="hidden" name="code" value="<?=$vars['code']?>">
                    <?= \Idno\Core\Idno::site()->actions()->signForm('/account/password/reset') ?>
                </div>
            </div>

        </form>

    </div>

</div>