<?=$this->draw('shell/toolbar/logged-in/items')?>
<!--<ul class="nav navbar-nav">-->
<li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="">
        <?= htmlspecialchars(\Idno\Core\Idno::site()->session()->currentUser()->getTitle())?>
        <?php
            $notifs = \Idno\Core\Idno::site()->session()->currentUser()->countUnreadNotifications();
            if ($notifs > 0) {
                echo "<span class=\"unread-notification-count\">$notifs</span>";
            }
        ?>
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
        <li><a href="<?=\Idno\Core\Idno::site()->session()->currentUser()->getDisplayURL()?>">Profile</a></li>
        <!--<li><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/notifications">Notifications</a></li>-->
        <?=$this->draw('shell/toolbar/personal/items')?>
        <li><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/">Account Settings</a></li>
        <?php if (\Idno\Core\Idno::site()->session()->currentUser()->isAdmin()) { ?>
            <li><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/">Site Configuration</a></li>
        <?php } ?>
        <?php

                /*
                 * Alternative toolbar temporarily commented out
                 *

                <li><a href="<?=\Idno\Core\Idno::site()->session()->currentUser()->getDisplayURL()?>"><i class="fa fa-user" title="Your Profile"></i></a></li>

                <?=$this->draw('shell/toolbar/logged-in/items')?>

                <?php

                    if (\Idno\Core\Idno::site()->canWrite()) { ?>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog" title="Settings"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>profile/<?=\Idno\Core\Idno::site()->session()->currentUser()->getHandle()?>/edit">Edit Profile</a></li></li>
                            <li><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>account/settings/">Account Settings</a></li></li>
                            <?php if (\Idno\Core\Idno::site()->session()->currentUser()->isAdmin()) { ?>
                                <li><a href="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/">Site Configuration</a></li>
                            <?php } ?>
                        </ul>
                <?php }
                    */
        ?>
        <li><a href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>account/settings/feedback/" ><i class="fa fa-heart" title="Leave feedback"></i> Feedback</a></li>
        <?=$this->draw('shell/toolbar/logout')?>
    </ul>


</li>
<!--</ul>-->
