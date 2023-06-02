<form action="<?php echo \Idno\Core\Idno::site()->config()->getURL()?>admin/staticpages/categories/" method="post">

    <p>
        <textarea name="categories" style="width: 100%; height: 8em"><?php echo htmlspecialchars(implode("\n", $vars['categories']))?></textarea>
    </p>
    <p>
        <?php echo \Idno\Core\Idno::site()->actions()->signForm('/admin/staticpages') ?>
        <input type="submit" class="btn btn-primary" value="<?php echo \Idno\Core\Idno::site()->language()->_('Save'); ?>">
    </p>

</form>