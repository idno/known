<li><a href="<?=\Idno\Core\site()->session()->currentUser()->getURL()?>">Profile</a></li>

<?php if (\Idno\Core\site()->canEdit()) { ?>

<li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/">Account</a></li>

<?php if(\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>

<li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/">Configure</a></li>

<?php } ?>
<?php } ?>
<li><?=  \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'session/logout', 'Sign out', null, ['class' => '']);?></li>
