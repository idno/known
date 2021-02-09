<?php
    $user = \Idno\Core\Idno::site()->session()->currentUser();
if (empty($user->notifications['email'])) {
    $user->notifications['email'] = 'none';
}
?>
<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?php echo $this->draw('account/menu') ?>
        <h1>
            <?php echo \Idno\Core\Idno::site()->language()->_('Email notifications'); ?>
        </h1>

        <div class="explanation">
            <p>
                <?php echo \Idno\Core\Idno::site()->language()->_("Set how you'd like to be notified when someone stars or comments on your content."); ?>
            </p>
        </div>
        <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/notifications" method="post"
              class="form-horizontal"
              enctype="multipart/form-data">

            <div class="form-group">
                <div class="col-md-10">
                    <label> <?php echo \Idno\Core\Idno::site()->language()->_('Send email notifications'); ?> </label>

                    <div class="radio">
                        <label>
                            <input type="radio" name="notifications[email]" id="optionsRadios1"
                                   value="all" <?php if ($user->notifications['email'] == 'all') {
                                        echo 'checked';
} ?>>
                            <?php echo \Idno\Core\Idno::site()->language()->_('Whenever someone interacts with my content'); ?>
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="notifications[email]" id="optionsRadios2"
                                   value="comments" <?php if ($user->notifications['email'] == 'comments') {
                                        echo 'checked';
} ?>>
                            <?php echo \Idno\Core\Idno::site()->language()->_('Only when someone comments on my content'); ?>
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="notifications[email]" id="optionsRadios3"
                                   value="none" <?php if ($user->notifications['email'] == 'none') {
                                        echo 'checked';
} ?>>
                            <?php echo \Idno\Core\Idno::site()->language()->_('Never'); ?>
                        </label>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <div class="col-md-10">
                    <label><?php echo \Idno\Core\Idno::site()->language()->_('Ignored Domains'); ?></label>
                    <?php echo \Idno\Core\Idno::site()->language()->_('Do not send notifications for interactions originating from these domains (one domain per line)'); ?>
                    <textarea name="notifications[ignored_domains]" class="form-control"><?php
                    if (isset($user->notifications['ignored_domains'])) {
                        echo implode(PHP_EOL, $user->notifications['ignored_domains']);
                    } ?></textarea>
                </div>
            </div>

            <?php echo $this->draw('account/settings/notifications/methods'); ?>
            <div class="control-group">
                <div class="controls-save">
                    <button type="submit" class="btn btn-primary"><?php echo \Idno\Core\Idno::site()->language()->_('Save settings'); ?></button>
                </div>
            </div>
            <?php echo \Idno\Core\Idno::site()->actions()->signForm('/account/settings/notifications') ?>
        </form>
    </div>
</div>
