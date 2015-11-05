<div class="row">

    <div class="col-md-8 col-md-offset-2">

        <h1>
            Reset your password
        </h1>
        <p>
            Forgot your password? Don't worry! It happens to the best of us.</p> 
           <p>Just enter the email address associated with your
            <?=\Idno\Core\Idno::site()->config()->title?> account below, and we'll send you a top secret
            link to your email account so that you can create a new password.
        </p>


        <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/password" method="post">

            <div class="row">
            <div class="form-group col-md-6">
                <label class="control-label" for="inputName">Your email address</label>
                    <!--<small>The address associated with your <?=\Idno\Core\Idno::site()->config()->title?> account.</small>-->
                    <input type="email" id="inputName" placeholder="me@awesome.com" class="form-control" name="email" value="" required>
            </div>
            </div>
            <div class="row">
            <div class="form-group col-md-6">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Send password reset email</button>
                    <?= \Idno\Core\Idno::site()->actions()->signForm('/account/password') ?>
                </div>
            </div>
            </div>

        </form>

    </div>

</div>