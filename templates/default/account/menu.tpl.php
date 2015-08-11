<ul class="nav nav-tabs">
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/') echo 'class="active"'; ?> role="presentation"><a
            href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/">Settings</a></li>
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/notifications/') echo 'class="active"'; ?>
        role="presentation"><a
            href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/notifications/">Email
            notifications</a></li>
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/tools/') echo 'class="active"'; ?> role="presentation">
        <a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/tools/">Tools and Apps</a></li>
    <?= $this->draw('account/menu/items') ?>
    <?php

        if (\Idno\Core\site()->config()->show_directory) {

            ?>
        <li <?php if ($_SERVER['REQUEST_URI'] == '/directory/') echo 'class="active"'; ?> role="presentation">
            <a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>directory/">Member Directory</a></li>
            <?php

        }

    ?>
</ul>