<?php /* @var \knownPlugins\Text\Entry $vars['object'] */ ?>
<div class="edit">
    <p>
        <a href="<?=$vars['object']->getEditURL()?>">Edit</a>
        <?=  \known\Core\site()->actions()->createLink($vars['object']->getDeleteURL(), 'Delete', array(), array('method' => 'POST'));?>
    </p>
</div>