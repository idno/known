<p>
    To get started exporting your content, click the button below. It'll take a while, but you can leave this page.
    This page will update once the export is complete, and you'll be able to download the archive right here.
</p>
<form action="<?=\Idno\Core\site()->config()->getURL()?>admin/export/generate/" method="post">
    <p style="text-align: center; margin-top: 3em;">
        <input type="submit" class="btn btn-primary" value="Start exporting your data">
        <?php

            echo \Idno\Core\site()->actions()->signForm(\Idno\Core\site()->config()->getURL() . 'admin/export/generate');

        ?>
    </p>
</form>