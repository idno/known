
<li class="<?php if (strpos(\Idno\Core\Idno::site()->currentPage()->currentUrl(), \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/notifications') !== false) echo 'active' ?>">
    <a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/notifications">
        <i class="fa fa-bell"></i><?php echo \Idno\Core\Idno::site()->language()->_('Notifications'); ?><?php
        $notifs = \Idno\Core\Idno::site()->session()->currentUser()->countUnreadNotifications();

        if ($notifs > 0) {
            echo " <span class=\"badge\">$notifs</span>";
        }
        ?></a>
</li>
<li class="<?php if ((strpos(\Idno\Core\Idno::site()->currentPage()->currentUrl(), \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/') !== false) && (strpos(\Idno\Core\Idno::site()->currentPage()->currentUrl(), \Idno\Core\Idno::site()->config()->getDisplayURL() . 'account/notifications') === false)) echo 'active' ?>">
    <a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/"><i class="fa fa-cog"></i><?php echo \Idno\Core\Idno::site()->language()->_('Account Settings'); ?></a>
</li>

    <?php if (\Idno\Core\Idno::site()->session()->currentUser()->isAdmin()) { ?>
        <li class="<?php if (strpos(\Idno\Core\Idno::site()->currentPage()->currentUrl(), \Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/') !== false) echo 'active' ?>">
            <a href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/"><i class="fa fa-cogs"></i><?php echo \Idno\Core\Idno::site()->language()->_('Site Configuration'); ?></a>
        </li>
    <?php } ?>

<li>
    <a href="<?php echo \Idno\Core\Idno::site()->session()->currentUser()->getURL(); ?>"><img class="u-photo" src="<?php echo  \Idno\Core\Idno::site()->session()->currentUser()->getIcon(); ?>" alt="<?php echo htmlspecialchars(\Idno\Core\Idno::site()->session()->currentUser()->getTitle()) ?>" /></a> 
</li>

