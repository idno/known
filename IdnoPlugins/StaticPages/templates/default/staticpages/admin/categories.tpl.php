<form action="<?=\Idno\Core\site()->config()->getURL()?>admin/staticpages/categories/" method="post">

    <p>
        <textarea name="categories" style="width: 100%; height: 8em"><?=htmlspecialchars(implode("\n",$vars['categories']))?></textarea>
    </p>
    <p>
        <?= \Idno\Core\site()->actions()->signForm('/admin/staticpages') ?>
        <input type="submit" class="btn btn-primary" value="Save">
    </p>

</form>