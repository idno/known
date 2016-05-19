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
            <div class="row">
                <div class="col-md-2">
                    <p><label class="control-label" for="allposts"><strong>Include private posts?</strong></label></p>
                </div>
                <div class="config-toggle col-md-4">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Yes" data-off="No"
                           name="allposts" id="allposts"
                           value="1">
                </div>
                <div class="col-md-6">
                    <p class="config-desc">Platforms like WordPress may assume that all your posts should be displayed publicly.
                        In order to protect your privacy, you may wish to just download your public posts.</p>
                </div>
            </div>
            <p>
                <input type="submit" class="btn btn-primary" value="Download RSS file">
            </p>
            <?php

                echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/rss');

            ?>
        </form>

    </div>
</div>