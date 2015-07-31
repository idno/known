<?=$this->draw('shell/toolbar/logged-in/items')?>
<li><a href="<?=\Idno\Core\site()->session()->currentUser()->getDisplayURL()?>">Profile</a></li>
<li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/">Settings</a></li></li>
<?php if (\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>
    <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/">Site Configuration</a></li>
<?php } ?>
<?php

    /*
     * Alternative toolbar temporarily commented out
     *

    <li><a href="<?=\Idno\Core\site()->session()->currentUser()->getDisplayURL()?>"><i class="fa fa-user" title="Your Profile"></i></a></li>

    <?=$this->draw('shell/toolbar/logged-in/items')?>

    <?php

        if (\Idno\Core\site()->canWrite()) { ?>

        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog" title="Settings"></i></a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>profile/<?=\Idno\Core\site()->session()->currentUser()->getHandle()?>/edit">Edit Profile</a></li></li>
                <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/">Account Settings</a></li></li>
                <?php if (\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>
                    <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/">Site Configuration</a></li>
                <?php } ?>
            </ul>
    <?php }
        */
?>
<li><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>account/settings/feedback/" ><i class="fa fa-heart" title="Leave feedback"></i></a></li>
<li><?= \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'session/logout', 'Sign out', null, array('class' => '')); ?></li>
