<div class="row">
    <div class="span10 offset1">
	            <?php

            echo $this->draw('admin/menu');

        ?>
        <h1>
            Export data
        </h1>


        <p class="explanation">
            Known gives you an archive containing all of your site's data. The export includes every file you upload, every site user, and
            every post you publish. You can then import these into other Known sites or process them using other
            software.
        </p>
        <?php

            if (empty($vars['export_in_progress']) && !empty($vars['export_file_id'])) {

                echo $this->draw('admin/export/download');

            } else if (empty($vars['export_last_requested']) || (time() - $vars['export_last_requested'] >= (60*5))) {

                echo $this->draw('admin/export/generate');

            } else {

                echo $this->draw('admin/export/wait');

            }

        ?>

    </div>
</div>