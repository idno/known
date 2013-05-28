<li><span class="icon-container"><a href="<?=\Idno\Core\site()->session()->currentUser()->getURL()?>"><img src="<?=\Idno\Core\site()->session()->currentUser()->getIcon()?>" alt="<?=htmlspecialchars(\Idno\Core\site()->session()->currentUser()->getTitle())?>" /></a></span></li>
<li><a href="/account/settings/">Settings</a></li>
<?php if(\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>
<li><a href="/admin/">Administration</a></li>
<?php }?>
<li><?=  \Idno\Core\site()->actions()->createLink('/session/logout', 'Sign out');?></li>