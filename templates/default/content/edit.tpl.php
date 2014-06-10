<?php /* @var \IdnoPlugins\Text\Entry $vars['object'] */ ?>
<?php

    if ($vars['object']->canEdit()) {

?>

        <a href="<?=$vars['object']->getEditURL()?>" class="edit">Edit</a>
        <?=  \Idno\Core\site()->actions()->createLink($vars['object']->getDeleteURL(), 'Delete', [], ['method' => 'POST', 'class' => 'edit']);?>

<?php

    }

?>