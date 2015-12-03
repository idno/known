<ul class="nav nav-tabs">
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/') echo 'class="active"'; ?> role="presentation"><a
            href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/">Settings</a></li>
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/notifications/') echo 'class="active"'; ?>
        role="presentation"><a
            href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/notifications/">Email
            notifications</a></li>
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/tools/') echo 'class="active"'; ?> role="presentation">
        <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/tools/">Tools and Apps</a></li>
    <?= $this->draw('account/menu/items') ?>
    <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/tools/') echo 'class="active"'; ?> role="presentation">
        <a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/export/">Export Data</a></li>
</ul>