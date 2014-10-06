<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="span10 offset1">
        <h1>
            Email notifications
        </h1>
        <?= $this->draw('account/menu') ?>
        <div class="explanation">
            <p>
                Set how you'd like to be notified when someone stars or comments on your content.
            </p>
        </div>
        <form action="<?= \Idno\Core\site()->config()->url ?>account/settings/notifications" method="post" class="form-horizontal"
              enctype="multipart/form-data">
            <div class="control-group">
                <label class="control-label" for="email">Send email notifications</label>
                <div class="controls">
                    <select name="notifications[email]" id="email" class="span4">
                        <option value="all" <?php if ($user->notifications[email] == 'all') { echo 'selected'; } ?>>Whenever someone interacts with my content</option>
                        <option value="comments" <?php if ($user->notifications[email] == 'comment') { echo 'selected'; } ?>>Only when someone comments on my content</option>
                        <option value="none" <?php if ($user->notifications[email] == 'none') { echo 'selected'; } ?>>Never</option>
                    </select>
                </div>
            </div>
            <?=$this->draw('account/settings/notifications/methods');?>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/account/settings/notifications') ?>
        </form>
    </div>
</div>