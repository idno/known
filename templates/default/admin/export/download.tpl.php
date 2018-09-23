<p style="text-align: left">
    <a class="btn btn-primary" href="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>file/<?php echo $vars['export_file_id']?>/<?php echo $vars['export_filename']?>"><?php echo \Idno\Core\Idno::site()->language()->_('Download your data'); ?></a><br>
    <small><?php echo \Idno\Core\Idno::site()->language()->_('Generated'); ?> <time class="dt-published"
                           datetime="<?php echo date('c', $vars['export_last_requested']) ?>"><?php echo date('c', $vars['export_last_requested']) ?></time></small>
</p>
<p style="margin-top: 2em">
    <?php echo \Idno\Core\Idno::site()->language()->_('You can also regenerate a new copy of your data archive.'); ?>
</p>
<form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/generate/" method="post">
    <p style="text-align: left; margin-top: 1em;">
        <input type="submit" class="btn btn-primary" value="<?php echo \Idno\Core\Idno::site()->language()->_('Re-archive your data'); ?>">
        <?php

            echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/generate');

        ?>
    </p>
</form>