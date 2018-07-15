<div class="row">

    <div class="col-md-8 col-md-offset-2">

        <h1><?= \Idno\Core\Idno::site()->language()->_('Your password reset email has been sent'); ?></h1>

    </div>

</div>
<div class="row" style="margin-top: 2em">

    <div class="col-md-8 col-md-offset-2">

        <p>
            <?= \Idno\Core\Idno::site()->language()->_("Check your inbox! You'll find an email containing a special link that will help you reset your password."); ?>
        </p>
        <p>
            <?= \Idno\Core\Idno::site()->language()->_("Did you remember your password? That's okay too. You can <a href=\"%s\">click here to sign in.</a>", [\Idno\Core\Idno::site()->config()->getDisplayURL() . 'session/login']); ?>
        </p>

    </div>

</div>