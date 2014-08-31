<div class="row">
    <div class="span10 offset1">
        <h1>User Management</h1>
        <?= $this->draw('admin/menu') ?>

        <div class="explanation">
            <p>
                Manage users in the system, and invite new ones.
            </p>

        </div>
    </div>
</div>
<div class="row">
    <div class="span10 offset1">
        <h3>Site users:</h3>
        <p>
            The following users are members of this site.
        </p>
    </div>
</div>
<div class="pane">

    <?php

        if (!empty($vars['users']) && is_array($vars['users'])) {
            foreach($vars['users'] as $user) {
                if ($user instanceof \Idno\Entities\User) {
                    $handle = $user->getHandle();
                    if (!empty($handle)) {
                /* @var \Idno\Entities\User $user */
?>

    <div class="row <?= strtolower(str_replace('\\', '-', get_class($user))); ?>">
        <div class="span3 offset1">
            <p>
                <img src="<?=$user->getIcon()?>" style="width: 35px; float: left; margin-right: 10px; margin-top: 3px; margin-bottom: 3em">
                <a href="<?=$user->getURL()?>"><?=htmlentities($user->getTitle())?></a> (<a href="<?=$user->getURL()?>"><?=$user->getHandle()?></a>)<br>
                <small><?=$user->email?></small>
            </p>
        </div>
        <div class="span2">
            <p>
                <small><strong>Joined</strong><br><time datetime="<?=date('r',$user->created)?>" class="dt-published"><?=date('r',$user->created)?></time></small>
            </p>
        </div>
        <div class="span2">
            <p>
                <small><strong>Updated</strong><br><time datetime="<?=date('r',$user->updated)?>" class="dt-published"><?=date('r',$user->updated)?></time></small>
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
                                <?=  \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->url . 'admin/users', 'Remove rights', ['user' => $user->getUUID(), 'action' => 'remove_rights'], ['class' => '']);?>
                            <?php
                        } else {
                            ?>
                                Standard member<br>
                            <?=  \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->url . 'admin/users', 'Make admin', ['user' => $user->getUUID(), 'action' => 'add_rights'], ['class' => '']);?>
                            <?php
                        }
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

<div class="row">
    <div class="span10 offset1">

        <form action="<?= \Idno\Core\site()->config()->getURL() ?>admin/users" method="post">

            <h3>Invite users:</h3>

            <p>
                To invite users to the system, enter one or more email addresses below.
            </p>

            <textarea name="invitation_emails" class="span8"></textarea>

            <p>
                <input type="submit" class="btn btn-primary" value="Invite">
                <input type="hidden" name="action" value="invite_users">
                <?= \Idno\Core\site()->actions()->signForm('/admin/users')?>
            </p>

        </form>

    </div>
</div>
