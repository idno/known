<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav">
            <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/" ><?= \Idno\Core\Idno::site()->language()->_('Account settings'); ?></a></li>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/notifications/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/notifications/" ><?= \Idno\Core\Idno::site()->language()->_('Notifications'); ?></a></li>
            <?php /*

            This is an early development feature and is not ready to be exposed.
            */
            if (\Idno\Core\Idno::site()->config()->experimental) {
                ?>
            <li <?php if ($_SERVER['REQUEST_URI'] == '/account/settings/following/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/following/" ><?= \Idno\Core\Idno::site()->language()->_('Following'); ?></a></li>
            <?php } ?>
            <?php echo $this->draw('account/menu/items')?>
        </ul>
    </div>
</div>