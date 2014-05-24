<div class="row">

    <div class="span10 offset1">
        <h1>User Management</h1>
        <?= $this->draw('admin/menu') ?>

        <div class="explanation">
            <p>
                Manage users in the system, and invite new ones.
            </p>

            <p>
                <em>Coming soon: a panel of existing users that you can make into site administrators etc.</em>
            </p>
        </div>
    </div>
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
                <?= \Idno\Core\site()->actions()->signForm('/admin/users')?>
            </p>

        </form>

    </div>
</div>
