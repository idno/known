<div class="edit">
    <p>
        <a href="<?=$vars['object']->getEditURL()?>">Edit</a>
        <?=  \Idno\Core\site()->actions()->createLink($vars['object']->getURL(), 'Delete', array(), array('method' => 'DELETE'));?>
    </p>
</div>