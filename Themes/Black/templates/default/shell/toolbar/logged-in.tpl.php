<li><a href="<?= \Idno\Core\site()->session()->currentUser()->getDisplayURL() ?>">Profile</a></li>

<?php if (\Idno\Core\site()->canWrite()) { ?>

    <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>account/settings/">Settings</a></li>

    <?php if (\Idno\Core\site()->session()->currentUser()->isAdmin()) { ?>

        <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>admin/">Site configuration</a></li>

    <?php } ?>
<?php } ?>
<li><a href="<?=\Idno\Core\site()->config()->getDisplayURL()?>account/settings/feedback/" ><icon class="icon-heart"></icon></a></li>
<li><?= \Idno\Core\site()->actions()->createLink(\Idno\Core\site()->config()->getDisplayURL() . 'session/logout', 'Sign out', null, ['class' => '']); ?></li>
