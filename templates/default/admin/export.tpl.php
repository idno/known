<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <?php

            echo $this->draw('admin/menu');

        ?>
        <h1>
            <?php echo \Idno\Core\Idno::site()->language()->_('Export content'); ?>
        </h1>

    </div>
</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <p class="explanation">
            <?php echo \Idno\Core\Idno::site()->language()->_('Export your published Idno content as an RSS file. You can import this file into Idno or WordPress.'); ?>
        </p>
        <h3>
            <?php echo \Idno\Core\Idno::site()->language()->_('Generate RSS file'); ?>
        </h3>
        <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/rss" method="post">
            <div class="row">
                <div class="col-md-3">
                    <p><label class="control-label" for="allposts"><strong><?php echo \Idno\Core\Idno::site()->language()->_('Include private posts?'); ?></strong></label></p>
                </div>
                <div class="config-toggle col-md-3">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info"
                           data-on="<?php echo \Idno\Core\Idno::site()->language()->_('Yes'); ?>"
                           data-off="<?php echo \Idno\Core\Idno::site()->language()->_('No'); ?>"
                           name="allposts" id="allposts"
                           value="1">
                </div>
                <div class="col-md-6">
                    <p class="config-desc"><?php echo \Idno\Core\Idno::site()->language()->_('Some platforms may assume that your content should be displayed publicly. To protect your privacy, you may wish to only download your public content.'); ?></p>
                </div>
            </div>
            <p>
                <input type="submit" class="btn btn-primary" value="<?php echo \Idno\Core\Idno::site()->language()->_('Download file'); ?>">
            </p>
            <?php

                echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/rss');

            ?>
        </form>

        <hr>

        <h3>
            <?php echo \Idno\Core\Idno::site()->language()->_('Generate WordPress (WXR) file'); ?>
        </h3>
        <p class="explanation">
            <?php echo \Idno\Core\Idno::site()->language()->_('Export your content as a WordPress eXtended RSS (WXR) file. This format includes additional metadata such as post dates, slugs, and comments, and is the standard format for importing into WordPress.'); ?>
        </p>
        <form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL()?>admin/export/wxr" method="post">
            <div class="row">
                <div class="col-md-3">
                    <p><label class="control-label" for="allposts_wxr"><strong><?php echo \Idno\Core\Idno::site()->language()->_('Include private posts?'); ?></strong></label></p>
                </div>
                <div class="config-toggle col-md-3">
                    <input type="checkbox" data-toggle="toggle" data-onstyle="info"
                           data-on="<?php echo \Idno\Core\Idno::site()->language()->_('Yes'); ?>"
                           data-off="<?php echo \Idno\Core\Idno::site()->language()->_('No'); ?>"
                           name="allposts" id="allposts_wxr"
                           value="1">
                </div>
                <div class="col-md-6">
                    <p class="config-desc"><?php echo \Idno\Core\Idno::site()->language()->_('Some platforms may assume that your content should be displayed publicly. To protect your privacy, you may wish to only download your public content.'); ?></p>
                </div>
            </div>
            <p>
                <input type="submit" class="btn btn-primary" value="<?php echo \Idno\Core\Idno::site()->language()->_('Download WXR file'); ?>">
            </p>
            <?php

                echo \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/wxr');

            ?>
        </form>

    </div>
</div>
