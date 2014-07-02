<div class="row">

    <div class="span10 offset1">

        <h1>
            Reset your password
        </h1>
        <p>
            If you've forgotten your password, enter the email address associated with your
            <?=\Idno\Core\site()->config()->title?> account below, and we'll send you a special
            link to your email account that will help you to register a new one.
        </p>

    </div>

</div>
<div class="row" style="margin-top: 2em">

    <div class="span10 offset1">

        <form action="<?=\Idno\Core\site()->config()->url?>account/password" method="post" class="form-horizontal">

            <div class="control-group">
                <label class="control-label" for="inputUsername">Your email address<br />
                    <small>The address associated with your <?=\Idno\Core\site()->config()->title?> account.</small></label>
                <div class="controls">
                    <input type="email" id="inputName" placeholder="Email" class="span4" name="email" value="" required>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Send password reset email</button>
                    <?= \Idno\Core\site()->actions()->signForm('/account/password') ?>
                </div>
            </div>

        </form>

    </div>

</div>