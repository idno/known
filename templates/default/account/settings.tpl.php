<?php
    $user = \Idno\Core\Idno::site()->session()->currentUser();
?>
<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('account/menu') ?>
        <h1>
            <?= \Idno\Core\Idno::site()->language()->_('Account Settings'); ?>
        </h1>

        <div class="explanation">
            <p>
                <?= \Idno\Core\Idno::site()->language()->_('Change your user account settings here. You may also want to'); ?> <a
                    href="<?= \Idno\Core\Idno::site()->session()->currentUser()->getDisplayURL() ?>/edit/"><?= \Idno\Core\Idno::site()->language()->_('edit your profile'); ?></a>.
            </p>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <form class="form-horizontal admin" action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/" method="post">


            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputName"><?= \Idno\Core\Idno::site()->language()->_('Your name'); ?></label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputName', 'value' => $user->getTitle(), 'class' => 'input col-md-4 form-control', 'name' => 'name', 'placeholder' => \Idno\Core\Idno::site()->language()->_('Your name')])->draw('forms/input/text'); ?>
                </div>

                <div class="col-md-5">
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputEmail"><?= \Idno\Core\Idno::site()->language()->_('Your email address'); ?></label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputEmail', 'value' => $user->email, 'class' => 'input col-md-4 form-control', 'name' => 'email', 'placeholder' => \Idno\Core\Idno::site()->language()->_('Your email address')])->draw('forms/input/email'); ?>
                </div>
                <div class="col-md-5 config-desc">
                    <?= \Idno\Core\Idno::site()->language()->_('Site notifications will be sent here.'); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputPassword"><?= \Idno\Core\Idno::site()->language()->_('Your password'); ?><br>
                    </label>
                </div>
                <div class="col-md-4">
                    <?= $this->__(['id' => 'inputPassword', 'class' => 'input col-md-4 form-control', 'name' => 'password', 'placeholder' => \Idno\Core\Idno::site()->language()->_('Password'), 'autocomplete' => 'off', 'value' => ''])->draw('forms/input/password'); ?>
                </div>
                <div class="col-md-5 config-desc">
                    <?= \Idno\Core\Idno::site()->language()->_("Leave this blank if you don't want to change it."); ?>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-md-3">
                    <label class="control-label" for="inputTimezone"><?= \Idno\Core\Idno::site()->language()->_('Your timezone'); ?><br>
                    </label>
                </div>
                <div class="col-md-4">
                    <?= $this->__([
                        'id' => 'inputTimezone', 
                        'class' => 'input-timezone input col-md-4 form-control', 
                        'blank-default' => true, 
                        'name' => 'timezone', 
                        'placeholder' => \Idno\Core\Idno::site()->language()->_('Timezone'), 
                        'value' => $user->timezone
                    ])->draw('forms/input/timezones'); ?>
                </div>
                <div class="col-md-5 config-desc">
                    <?= \Idno\Core\Idno::site()->language()->_('Specify your timezone.'); ?>
                </div>
            </div>

            <div class="controls-save">
                <button type="submit" class="btn btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Save updates'); ?></button>
            </div>

            <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings') ?>
        </form>


    </div>
</div>
