<?php
    $user = \Idno\Core\Idno::site()->session()->currentUser();
    if (empty($user->notifications['email'])) {
        $user->notifications['email'] = 'none';
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
        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/notifications" method="post"
              class="form-horizontal"
              enctype="multipart/form-data">

            <div class="form-group">
                <div class="col-md-10">
                    <label> Send email notifications </label>

                    <div class="radio">
                        <label>
                            <input type="radio" name="notifications[email]" id="optionsRadios1"
                                   value="all" <?php if ($user->notifications['email'] == 'all') {
                                echo 'checked';
                            } ?>>
                            Whenever someone interacts with my content
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="notifications[email]" id="optionsRadios2"
                                   value="comments" <?php if ($user->notifications['email'] == 'comments') {
                                echo 'checked';
                            } ?>>
                            Only when someone comments on my content
                        </label>
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="notifications[email]" id="optionsRadios3"
                                   value="none" <?php if ($user->notifications['email'] == 'none') {
                                echo 'checked';
                            } ?>>
                            Never
                        </label>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <div class="col-md-10">
                    <label>Ignored Domains</label>
                    Do not send notifications for interactions originating from these domains (one domain per line)
                    <textarea name="notifications[ignored_domains]" class="form-control"><?php
                       if (isset($user->notifications['ignored_domains'])) {
                         echo implode(PHP_EOL, $user->notifications['ignored_domains']);
                       } ?></textarea>
                </div>
            </div>

            <?= $this->draw('account/settings/notifications/methods'); ?>
            <div class="control-group">
                <div class="controls-save">
                    <button type="submit" class="btn btn-primary">Save settings</button>
                </div>
            </div>
            <?= \Idno\Core\Idno::site()->actions()->signForm('/account/settings/notifications') ?>
        </form>
    </div>
</div>
