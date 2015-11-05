<?php /* @var \IdnoPlugins\Text\Entry $vars['object'] */ ?>
<?php

    if ($vars['object']->canEdit()) {

?>

        <a href="<?=$vars['object']->getEditURL()?>" class="edit">Edit</a>
        <?=  \Idno\Core\Idno::site()->actions()->createLink($vars['object']->getDeleteURL(), 'Delete', array(), array('method' => 'POST', 'class' => 'edit', 'confirm' => true, 'confirm-text' => 'Are you sure you want to permanently delete this entry?'));?>

<?php

    }

?>