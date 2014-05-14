<div class="edit edit-annotation">
    <p>
        <?php /* TODO <a href="<?=$vars['object']->getEditURL()?>">Edit</a> */ ?>
        <?=  \known\Core\site()->actions()->createLink($vars['annotation_permalink'] . '/delete/', 'Delete', array(), array('method' => 'POST'));?>
    </p>
</div>