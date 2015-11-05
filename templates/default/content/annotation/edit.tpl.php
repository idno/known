<div class="edit edit-annotation">
    <p>
        <?=  \Idno\Core\Idno::site()->actions()->createLink($vars['annotation_permalink'] . '/delete/', 'Delete', array(), array('method' => 'POST'));?>
    </p>
</div>