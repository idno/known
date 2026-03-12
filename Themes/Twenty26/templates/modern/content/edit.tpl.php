<?php /* @var \Idno\Common\Entity $vars['object'] */ ?>
<?php

if ($vars['object']->canEdit()) {

    ?>
        <a href="<?= $vars['object']->getEditURL() ?>" class="idno-entry-action"><?= \Idno\Core\Idno::site()->language()->_('Edit') ?></a>
    <?= \Idno\Core\Idno::site()->actions()->createLink(
        $vars['object']->getDeleteURL(),
        \Idno\Core\Idno::site()->language()->_('Delete'),
        [],
        [
            'method' => 'POST',
            'class' => 'idno-entry-action',
            'confirm' => true,
            'confirm-text' => \Idno\Core\Idno::site()->language()->_('Are you sure you want to permanently delete this entry?')
        ]
    ) ?>
    <?= $this->draw('content/entity/' . $vars['object']->getEntityTypeName() . '/edit') ?>
    <?php

}
