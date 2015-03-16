<?php
    $user = \Idno\Core\site()->session()->currentUser();
    if (empty($user->notifications[email])) {
        $user->notifications[email] = 'none';
    }
?>
<div class="row">

    <div class="col-md-10 col-md-offset-1">
        <?= $this->draw('account/menu') ?>
        <h1>
            Email notifications
        </h1>

        <div class="explanation">
            <p>
                Set how you'd like to be notified when someone stars or comments on your content.
            </p>
        </div>
        <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/notifications" method="post"
              class="form-horizontal"
              enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-6">
                    <label class=""><strong>
                            Send email notifications
                        </strong>
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="radio col-md-6">
                    <label>
                        <input type="radio" name="notifications[email]" id="optionsRadios1"
                               value="all" <?php if ($user->notifications[email] == 'all') {
                            echo 'checked';
                        } ?>>
                        Whenever someone interacts with my content
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="radio col-md-6">
                    <label>
                        <input type="radio" name="notifications[email]" id="optionsRadios2"
                               value="comments" <?php if ($user->notifications[email] == 'comments') {
                            echo 'checked';
                        } ?>>
                        Only when someone comments on my content
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="radio col-md-6">
                    <label>
                        <input type="radio" name="notifications[email]" id="optionsRadios3"
                               value="none" <?php if ($user->notifications[email] == 'none') {
                            echo 'checked';
                        } ?>>
                        Never
                    </label>
                </div>
            </div>

            <?= $this->draw('account/settings/notifications/methods'); ?>
            <div class="control-group">
                <div class="controls-save">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/account/settings/notifications') ?>
        </form>
    </div>
</div>