<li><span class="icon-container"><a href="<?= \Idno\Core\site()->session()->currentUser()->getURL() ?>"><img
                src="<?= \Idno\Core\site()->session()->currentUser()->getIcon() ?>"
                alt="<?= htmlspecialchars(\Idno\Core\site()->session()->currentUser()->getTitle()) ?>"/></a></span></li>

<?=$this->draw('shell/toolbar/logged-in/items')?>

<?php if (\Idno\Core\site()->canWrite()) { ?>

    <li><a href="<?= \Idno\Core\site()->config()->url ?>account/settings/">Settings</a></li>

    <?php if (\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>

        <li><a href="<?= \Idno\Core\site()->config()->url ?>admin/">Site configuration</a></li>
        
        <li><a href="<?=\Idno\Core\site()->config()->url?>account/settings/feedback/" ><icon class="icon-heart"></icon></a></li>

    <?php } ?>
<?php } ?>
<li><?= \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->url . 'session/logout', 'Sign out', null, ['class' => '']); ?></li>
