<?php
    ob_start();
?>
    <p class="idno-admin-description">
        <?= \Idno\Core\Idno::site()->language()->_('Export your published Idno content as an RSS file. You can import this file into Idno or WordPress.') ?>
    </p>

    <div class="idno-admin-card">
        <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Generate RSS file') ?></h2>
        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/export/rss" method="post">
            <div class="idno-form-group">
                <div class="idno-checkbox-group">
                    <input type="checkbox" name="allposts" id="allposts" value="1">
                    <label for="allposts"><?= \Idno\Core\Idno::site()->language()->_('Include private posts?') ?></label>
                </div>
                <p class="idno-form-help"><?= \Idno\Core\Idno::site()->language()->_('Some platforms may assume that your content should be displayed publicly. To protect your privacy, you may wish to only download your public content.') ?></p>
            </div>
            <div class="idno-form-group">
                <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Download file') ?></button>
            </div>
            <?= \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/rss') ?>
        </form>
    </div>

    <div class="idno-admin-card">
        <h2 class="idno-admin-card-title"><?= \Idno\Core\Idno::site()->language()->_('Generate WordPress (WXR) file') ?></h2>
        <p>
            <?= \Idno\Core\Idno::site()->language()->_('Export your content as a WordPress eXtended RSS (WXR) file. This format includes additional metadata such as post dates, slugs, and comments, and is the standard format for importing into WordPress.') ?>
        </p>
        <form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>admin/export/wxr" method="post">
            <div class="idno-form-group">
                <div class="idno-checkbox-group">
                    <input type="checkbox" name="allposts" id="allposts_wxr" value="1">
                    <label for="allposts_wxr"><?= \Idno\Core\Idno::site()->language()->_('Include private posts?') ?></label>
                </div>
                <p class="idno-form-help"><?= \Idno\Core\Idno::site()->language()->_('Some platforms may assume that your content should be displayed publicly. To protect your privacy, you may wish to only download your public content.') ?></p>
            </div>
            <div class="idno-form-group">
                <button type="submit" class="idno-btn idno-btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Download WXR file') ?></button>
            </div>
            <?= \Idno\Core\Idno::site()->actions()->signForm(\Idno\Core\Idno::site()->config()->getDisplayURL() . 'admin/export/wxr') ?>
        </form>
    </div>
<?php
    $content = ob_get_clean();
    echo $this->__([
        'body' => $content,
        'title' => \Idno\Core\Idno::site()->language()->_('Export Content')
    ])->draw('admin/shell');
?>
