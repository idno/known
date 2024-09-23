
        <ul class="nav nav-tabs">
            <li <?php if (\Idno\Core\Idno::site()->request()->getPathInfo() == '/account/settings/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/" ><?= \Idno\Core\Idno::site()->language()->_('Settings'); ?></a></li>
            <li <?php if (\Idno\Core\Idno::site()->request()->getPathInfo() == '/account/settings/notifications/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/notifications/" ><?= \Idno\Core\Idno::site()->language()->_('Email notifications'); ?></a></li>
            <li <?php if (\Idno\Core\Idno::site()->request()->getPathInfo() == '/account/settings/tools/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/tools/" ><?= \Idno\Core\Idno::site()->language()->_('Tools and Apps'); ?></a></li>
            <?php /*

            This is an early development feature and is not ready to be exposed.
            */
            if (\Idno\Core\Idno::site()->config()->experimental) {
                ?>
            <li <?php if (\Idno\Core\Idno::site()->request()->getPathInfo() == '/account/settings/following/') echo 'class="active"'; ?>><a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/following/" ><?= \Idno\Core\Idno::site()->language()->_('Following'); ?></a></li>
            <?php } ?>
            <?php echo $this->draw('account/menu/items')?>
        </ul>
