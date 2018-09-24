<form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>following/add" method="post">
    <p>
        <?php echo \Idno\Core\Idno::site()->language()->_('Add the address of a site to follow'); ?>:<br>
            <input type="text" name="url" value="" placeholder="https:// ..." style="width: 100%"><br>
        <input type="submit" class="btn btn-primary" value="Add" style="width: 15%">
        <?php echo \Idno\Core\Idno::site()->actions()->signForm('following/add')?>
    </p>
</form>