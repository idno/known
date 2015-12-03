<div class="row">
    <div class="col-md-10 col-md-offset-1">
	            <?php

            echo $this->draw('admin/menu');

        ?>
        <h1>
            Export data
        </h1>

    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">

        <h2>
            Full Data Package
        </h2>

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

        <h2 style="margin-top: 3em">
            Export to WordPress
        </h2>

        <p class="explanation">
            You can download an RSS version of every post in your Known site. This file is suitable for importing
            into content management systems like WordPress, or another Known site.
        </p>
        <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/rss" method="post">
            <p>
                <input type="submit" class="btn btn-primary" value="Download RSS file">
            </p>
            <?php

                echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/rss');

            ?>
            <p>
                <label><input type="checkbox" name="allposts" value="1"> Include private posts</label>
            </p>
        </form>

    </div>
</div>