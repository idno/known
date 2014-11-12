<li><span class="icon-container"><a href="<?= \Idno\Core\site()->session()->currentUser()->getURL() ?>"><img
                src="<?= \Idno\Core\site()->session()->currentUser()->getIcon() ?>"
                alt="<?= htmlspecialchars(\Idno\Core\site()->session()->currentUser()->getTitle()) ?>"/></a></span></li>

<?=$this->draw('shell/toolbar/logged-in/items')?>

<?php if (\Idno\Core\site()->canWrite()) { ?>

    <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/">Settings</a></li>

    <?php if (\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>

        <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/">Site configuration</a></li>

    <?php } ?>
<?php } ?>
<li><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>account/settings/feedback/" ><icon class="icon-heart"></icon></a></li>
<li><?= \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'session/logout', 'Sign out', null, array('class' => '')); ?></li>
