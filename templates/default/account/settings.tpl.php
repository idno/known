<?php
    $user = \Idno\Core\Idno::site()->session()->currentUser();
?>
<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('account/menu') ?>
        <h1>
            Account Settings
        </h1>

        <div class="explanation">
            <p>
                Change your user account settings here. You may also want to <a
                    href="<?= \Idno\Core\Idno::site()->session()->currentUser()->getDisplayURL() ?>/edit/">edit your
                    profile</a>.
            </p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <form class="form-horizontal admin" action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/" method="post">


            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputName">Your name</label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputName', 'value' => $user->getTitle(), 'class' => 'input col-md-4 form-control', 'name' => 'name', 'placeholder' => 'Your name'])->draw('forms/input/text'); ?>
                </div>

                <div class="col-md-5">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputHandle">Your username</label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputHandle', 'value' => $user->handle, 'class' => 'input col-md-4 form-control', 'name' => 'handle', 'placeholder' => 'Your username'])->draw('forms/input/text'); ?>
                </div>
                <div class="col-md-5">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputEmail">Your email address</label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputEmail', 'value' => $user->email, 'class' => 'input col-md-4 form-control', 'name' => 'email', 'placeholder' => 'Your email address'])->draw('forms/input/email'); ?>
                </div>
                <div class="col-md-5 config-desc">
                    Site notifications will be sent here.
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputPassword">Your password<br>
                    </label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputPassword', 'class' => 'input col-md-4 form-control', 'name' => 'password', 'placeholder' => 'Password', 'autocomplete' => 'off', 'value' => ''])->draw('forms/input/password'); ?>
                </div>
                <div class="col-md-5 config-desc">
                    Leave this blank if you don't want to change it.
                </div>
            </div>

            <div class="controls-save">
                <button type="submit" class="btn btn-primary">Save updates</button>
            </div>

            <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings') ?>
        </form>


    </div>
</div>