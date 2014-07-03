<li><a href="<?=\Idno\Core\site()->session()->currentUser()->getURL()?>">Profile</a></li>

<?php if (\Idno\Core\site()->canEdit()) { ?>

<li><a href="<?= \Idno\Core\site()->config()->url ?>account/settings/">Settings</a></li>

<?php if(\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>

<li><a href="<?= \Idno\Core\site()->config()->url ?>admin/">Administration</a></li>

<?php } ?>
<?php } ?>
<li><?=  \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->url . 'session/logout', 'Sign out', null, ['class' => '']);?></li>
