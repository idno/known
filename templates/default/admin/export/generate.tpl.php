<hr>
<p>
    To start exporting your content, click the button below. It may take a while to generate the export file. You can leave this page while it's working. Once the export is complete, this page will update, and you'll be able to download the archive right here.
</p>
<form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/generate/" method="post">
    <p style="text-align: left; margin-top: 3em;">
        <input type="submit" class="btn btn-primary" value="Export your data">
        <?php

            echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/generate');

        ?>
    </p>
</form>