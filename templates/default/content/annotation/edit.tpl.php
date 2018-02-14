<div class="edit edit-annotation">
    <p>
        <?=  \Idno\Core\Idno::site()->actions()->createLink($vars['annotation_permalink'] . '/delete/', \Idno\Core\Idno::site()->language()->_('Delete'), array(), array('method' => 'POST'));?>
    </p>
</div>