<?php /* @var \IdnoPlugins\Text\Entry $vars['object'] */ ?>
<?php

    if ($vars['object']->canEdit()) {

?>

        <a href="<?=$vars['object']->getEditURL()?>" class="edit"><?= \Idno\Core\Idno::site()->language()->_('Edit'); ?></a>
        <?=  \Idno\Core\Idno::site()->actions()->createLink($vars['object']->getDeleteURL(), \Idno\Core\Idno::site()->language()->_('Delete'), array(), array('method' => 'POST', 'class' => 'edit edit-delete', 'confirm' => true, 'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to permanently delete this entry?')));?>

	<?= $this->draw('content/entity/' .  $vars['object']->getEntityTypeName() . '/edit'); ?>
<?php

    }

?>