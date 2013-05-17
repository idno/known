<?php /* @var \IdnoPlugins\Text\Entry $vars['object'] */ ?>
<div class="edit">
    <p>
        <a href="<?=$vars['object']->getEditURL()?>">Edit</a>
        <?=  \Idno\Core\site()->actions()->createLink($vars['object']->getDeleteURL(), 'Delete', array(), array('method' => 'POST'));?>
    </p>
</div>