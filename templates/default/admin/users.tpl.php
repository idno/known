<div class="row">
    <div class="span10 offset1">
	            <?= $this->draw('admin/menu') ?>
        <h1>Users</h1>


        <div class="explanation">
            <p>
                View the users registered for your site, and invite new users to join.
            </p>

        </div>
    </div>
</div>
<div class="row">
    <div class="span10 offset1">
        <h3>Manage site users</h3>
        <p>
            Your site has <strong><?=sizeof($vars['users'])?></strong> user<?php if (sizeof($vars['users']) != 1) { echo 's'; } ?>.
        </p>
    </div>
</div>
<div class="row">
    <div class="pane span10 offset1">

        <?php
        if (!empty($vars['users']) && is_array($vars['users'])) {
            foreach ($vars['users'] as $user) {
                if ($user instanceof \Idno\Entities\User) {
                    $handle = $user->getHandle();
                    if (!empty($handle)) {
                        /* @var \Idno\Entities\User $user */
                        ?>

                        <div class="row <?= strtolower(str_replace('\\', '-', get_class($user))); ?>">
                            <div class="span3">
                                <p>
                                    <img src="<?= $user->getIcon() ?>" style="width: 35px; float: left; margin-right: 10px; margin-left: 10px; margin-top: 3px; margin-bottom: 3em">
                                    <a href="<?= $user->getDisplayURL() ?>"><?= htmlentities($user->getTitle()) ?></a> (<a href="<?= $user->getDisplayURL() ?>"><?= $user->getHandle() ?></a>)<br>
                                    <small><?= $user->email ?></small>
                                </p>
                            </div>
                            <div class="span2">
                                <p>
                                    <small><strong>Joined</strong><br><time datetime="<?= date('r', $user->created) ?>" class="dt-published"><?= date('r', $user->created) ?></time></small>
                                </p>
                            </div>
                            <div class="span2">
                                <p>
                                    <small><strong>Last update posted</strong>
                                        <br>
                                        <?php 
                                        if ($feed  = \Idno\Entities\ActivityStreamPost::getFromX(null, ['owner' => $user->getUUID()], array(), 1, 0)) {
                                        ?>
                                        <time datetime="<?= date('r', $feed[0]->updated) ?>" class="dt-published"><?= date('r', $feed[0]->updated) ?></time>
                                        <?php } else {
                                            ?>
                                        Never
                                        <?php
                                        } ?>
                                    </small>
                                </p>
                            </div>
                            <div class="span2">
                                <p>
                                    <small>
                                        <?php
                                        if ($user instanceof \Idno\Entities\RemoteUser) {
                                            ?>
                                            Remote member
                                            <?php
                                        } else {
                                            if ($user->isAdmin()) {
                                                ?>
                                                <strong>Administrator</strong><br>
                                                <?php
                                                if ($user->getUUID() != \Idno\Core\site()->session()->currentUserUUID()) {
                                                    echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'admin/users', 'Remove rights', array('user' => $user->getUUID(), 'action' => 'remove_rights'), array('class' => ''));
                                                } else {
                                                    echo 'Yes';
                                                }
                                            } else {
                                                ?>
                                                Standard member<br>
                                                <?= \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'admin/users', 'Make admin', array('user' => $user->getUUID(), 'action' => 'add_rights'), array('class' => '')); ?>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </small>
                                </p>
                            </div>
                            <div class="span1">
                                <p style="padding-top: 20px;"><small>
                <?php
                if ($user->getUUID() != \Idno\Core\site()->session()->currentUserUUID()) {
                    echo \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'admin/users', '<i class="icon-remove"></i> Delete', array('user' => $user->getUUID(), 'action' => 'delete'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? This will delete this user and all their content.'));
                } else {
                    echo '&nbsp';
                }
                ?>
                                    </small></p>
                            </div>
                        </div>

                <?php
            }
        }
    }
}
?>

    </div>

</div>
<?php

    if (\Idno\Core\site()->config()->canAddUsers()) {

        ?>
        <div class="row">
            <div class="span10 offset1">

                <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/users" method="post">

                    <h3>Invite new users</h3>

                    <p>
                        To invite new users to the site, enter one or more email addresses below.
                    </p>

                    <textarea name="invitation_emails" class="span10" placeholder="friend@email.com"></textarea>

                    <p>
                        <input type="submit" class="btn btn-primary" value="Send invite">
                        <input type="hidden" name="action" value="invite_users">
                        <?= \Idno\Core\site()->actions()->signForm('/admin/users') ?>
                    </p>

                </form>

            </div>
        </div>
    <?php

    }