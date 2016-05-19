<div class="row">
    <div class="col-md-10 col-md-offset-1">
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
    <div class="col-md-10 col-md-offset-1">
        <h3>Manage site users</h3>

        <p>
            Your site has <strong><?= sizeof($vars['users']) ?></strong> user<?php if (sizeof($vars['users']) != 1) {
                echo 's';
            } ?>.
        </p>
    </div>
</div>
<div class="row">
    <div class="pane col-md-10 col-md-offset-1">

        <?php
            if (!empty($vars['users']) && is_array($vars['users'])) {
                foreach ($vars['users'] as $user) {
                    if ($user instanceof \Idno\Entities\User) {
                        $handle = $user->getHandle();
                        if (!empty($handle)) {
                            if (strlen($handle) > 18) {
                                $display_handle = substr($handle, 0, 16) . '...';
                            } else {
                                $display_handle = $handle;
                            }
                            /* @var \Idno\Entities\User $user */
                            ?>

                            <div class="row <?= strtolower(str_replace('\\', '-', get_class($user))); ?>">
                                <div class="col-sm-4 col-xs-12">
                                    <p class="user-tbl">
                                        <img src="<?= $user->getIcon() ?>">
                                        <a href="<?= $user->getDisplayURL() ?>"><?= htmlentities($user->getTitle()) ?></a>
                                        (<a href="<?= $user->getDisplayURL() ?>"><?= $display_handle ?></a>)<br>
                                        <small><?= $user->email ?></small>
                                    </p>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <p class="user-tbl">
                                        <small><strong>Joined</strong><br>
                                            <time datetime="<?= date('r', $user->created) ?>"
                                                  class="dt-published"><?= date('r', $user->created) ?></time>
                                        </small>
                                    </p>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <p class="user-tbl">
                                        <small><strong>Last update posted</strong>
                                            <br>
                                            <?php
                                                $feed = \Idno\Common\Entity::getFromX(null, ['owner' => $user->getUUID()], array(), 1, 0);
                                                if (!empty($feed) && is_array($feed)) {
                                                    ?>
                                                    <time datetime="<?= date('r', $feed[0]->updated) ?>"
                                                          class="dt-published"><?= date('r', $feed[0]->updated) ?></time>
                                                <?php } else {
                                                    ?>
                                                    Never
                                                    <?php
                                                } ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <p class="user-tbl">
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
                                                        if ($user->getUUID() != \Idno\Core\Idno::site()->session()->currentUserUUID()) {
                                                            echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', 'Remove rights', array('user' => $user->getUUID(), 'action' => 'remove_rights'), array('class' => ''));
                                                        } else {
                                                            echo 'Yes';
                                                        }
                                                    } else {
                                                        ?>
                                                        Standard member<br>
                                                        <?= \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', 'Make admin', array('user' => $user->getUUID(), 'action' => 'add_rights'), array('class' => '')); ?>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                        </small>
                                    </p>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                    <p class="user-tbl">
                                        <small>
                                            <?php
                                                if ($user->getUUID() != \Idno\Core\Idno::site()->session()->currentUserUUID()) {
                                                    if (\Idno\Core\Idno::site()->config()->emailIsBlocked($user->email)) {
                                                        echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-check-circle-o"></i> Clear', array('blocked_emails' => $user->email, 'action' => 'unblock_emails'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user will be able to log in and post again.')) . '<br>';
                                                    } else {
                                                        echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-ban"></i> Block', array('blocked_emails' => $user->email, 'action' => 'block_emails'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user will be logged out and will no longer be able to log in or post.')) . '<br>';
                                                    }
                                                    echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-times"></i> Delete', array('user' => $user->getUUID(), 'action' => 'delete'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? This will delete this user and all their content.'));
                                                } else {
                                                    echo '&nbsp';
                                                }
                                            ?>
                                        </small>
                                    </p>
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

    if (\Idno\Core\Idno::site()->config()->canAddUsers()) {

        ?>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/users" method="post">

                    <h3>Invite new users</h3>

                    <p>
                        To invite new users to the site, enter one or more email addresses below.
                    </p>

                    <textarea name="invitation_emails" class="form-control" placeholder="friend@email.com"></textarea>

                    <p>
                        <input type="submit" class="btn btn-primary" value="Send invite">
                        <input type="hidden" name="action" value="invite_users">
                        <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/users') ?>
                    </p>

                </form>

            </div>
        </div>

        <?php

    }
        /*
         * Temporarily removing this feature due to some security concerns
         *
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/users" method="post">

                    <h3>Create a new user</h3>

                    <p>
                        You should only do this for people whose email addresses you trust
                        from prior correspondence. An email will not be sent.
                    </p>

                    <div>
                        <input type="email" name="email" placeholder="Email address" required >
                        <input type="text" name="handle" placeholder="Username" required >
                        <input type="text" name="name" placeholder="Full name" required >
                        <input type="password" name="password1" placeholder="Password" required>
                        <input type="password" name="password2" placeholder="Password again" required>
                    </div>

                    <p>

                    </p>

                    <p>
                        <input type="submit" class="btn btn-primary" value="Add">
                        <input type="hidden" name="action" value="add_user">
                        <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/users') ?>
                    </p>

                </form>
            </div>
        </div>
        */

        if (!empty($vars['invitations'])) {

?>

            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <h3>Invitations</h3>
                </div>
            </div>
            <div class="row">
                <div class="pane col-md-10 col-md-offset-1">
                    <?php

                        foreach ($vars['invitations'] as $invitation) {

                            ?>
                            <div class="row ">
                                <div class="col-lg-3 ">
                                    <p class="user-tbl">
                                        <?= $invitation->email ?>
                                    </p>
                                </div>
                                <div class="col-lg-3 ">
                                    <p class="user-tbl">
                                        <small><strong>Sent</strong><br>
                                        <time datetime="<?= date('r', $invitation->created) ?>"
                                              class="dt-published"><?= date('r', $invitation->created) ?></time></small>
                                    </p>
                                </div>
                                <div class="col-lg-5">
                                    <p class="user-tbl" style="text-align: right">
                                        <small>
                                            <?php

                                                echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-refresh"></i> Resend', array('invitation_id' => $invitation->getID(), 'action' => 'resend_invitation'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user will receive a second email.')) . '<br>';

                                            ?>
                                            <?php

                                                echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-times"></i> Remove', array('invitation_id' => $invitation->getID(), 'action' => 'remove_invitation'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user won\'t be able to register.')) . '<br>';

                                            ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <?php

                        }

                    ?>
                </div>
            </div>

<?php

        }

?>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/users" method="post">

                    <h3>Block email addresses</h3>

                    <p>
                        By blocking email addresses, you prevent people using those email addresses from registering on
                        your site. Enter the email addresses you want to block below.
                    </p>

                    <textarea name="blocked_emails" class="form-control" placeholder="user@email.com"></textarea>

                    <p>
                        <input type="submit" class="btn btn-primary" value="Block email addresses">
                        <input type="hidden" name="action" value="block_emails">
                        <?= \Idno\Core\Idno::site()->actions()->signForm('/admin/users') ?>
                    </p>

                </form>

            </div>
        </div>
        <?php

        if ($blocked_emails = \Idno\Core\Idno::site()->config()->getBlockedEmails()) {

            ?>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <h3>Blocked email addresses</h3>
                </div>
            </div>
            <div class="row">
                <div class="pane col-md-10 col-md-offset-1">
                    <?php

                        foreach ($blocked_emails as $email) {

                            ?>
                            <div class="row ">
                                <div class="col-sm-4 col-xs-12">
                                    <p class="user-tbl">
                                        <?= $email ?>
                                    </p>
                                </div>
                                <div class="col-sm-8 col-xs-12">
                                    <p class="user-tbl" style="text-align: right">
                                        <small>
                                            <?php

                                                echo \Idno\Core\Idno::site()->actions()->createLink(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/users', '<i class="fa fa-times"></i> Remove block', array('blocked_emails' => $email, 'action' => 'unblock_emails'), array('class' => '', 'confirm' => true, 'confirm-text' => 'Are you sure? The user will be able to log in and post again.')) . '<br>';

                                            ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                            <?php

                        }

                    ?>
                </div>
            </div>
            <?php

        }

        ?>
        <?php
