<li><span class="icon-container"><a href="<?=\known\Core\site()->session()->currentUser()->getURL()?>"><img src="<?=\known\Core\site()->session()->currentUser()->getIcon()?>" alt="<?=htmlspecialchars(\known\Core\site()->session()->currentUser()->getTitle())?>" /></a></span></li>
<li><a href="<?= \known\Core\site()->config()->url ?>account/settings/">Settings</a></li>
<?php if(\known\Core\site()->session()->currentUser()->isAdmin()) { ?>
<li><a href="<?= \known\Core\site()->config()->url ?>admin/">Administration</a></li>
<?php }?>
<li><?=  \known\Core\site()->actions()->createLink(\known\Core\site()->config()->url . 'session/logout', 'Sign out', null, ['class' => '']);?></li>
