<p style="text-align: left">
    <a class="btn btn-primary" href="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>file/<?=$vars['export_file_id']?>/<?=$vars['export_filename']?>"><?= \Idno\Core\Idno::site()->language()->_('Download your data'); ?></a><br>
    <small><?= \Idno\Core\Idno::site()->language()->_('Generated'); ?> <time class="dt-published"
                           datetime="<?= date('c', $vars['export_last_requested']) ?>"><?= date('c', $vars['export_last_requested']) ?></time></small>
</p>
<p style="margin-top: 2em">
    <?= \Idno\Core\Idno::site()->language()->_('You can also regenerate a new copy of your data archive.'); ?>
</p>
<form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/generate/" method="post">
    <p style="text-align: left; margin-top: 1em;">
        <input type="submit" class="btn btn-primary" value="<?= \Idno\Core\Idno::site()->language()->_('Re-archive your data'); ?>">
        <?php

            echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/generate');

        ?>
    </p>
</form>