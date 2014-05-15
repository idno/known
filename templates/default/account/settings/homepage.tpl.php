<?php
    $user = \known\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="span10 offset1">
        <h1>
            Account settings
        </h1>
        <?= $this->draw('account/menu') ?>
        <div class="explanation">
            <p>
                Choose which of your content people will see by default when they visit your personal page.
                They can always use the content menu to find types of content that you choose not to display.
            </p>
        </div>

        <form action="<?= \known\Core\site()->config()->url ?>account/settings/homepage" method="post"
              class="form-horizontal" enctype="multipart/form-data">

            <div class="control-group">
                <div class="control-label">Content to display<br/>
                    <small>Choose which kinds of content display by default when people visit your personal page.
                    </small>
                </div>
                <div class="controls">
                    <?php

                        if (!empty($vars['content_types'])) {
                            foreach ($vars['content_types'] as $content_type) {
                                /* @var \known\Common\ContentType $content_type */
                                ?>

                                <div class="content-type">
                                    <p>
                                        <input type="checkbox" name="default_feed_content[]"
                                               id="homepage_toggle_<?= $content_type->getCategoryTitleSlug() ?>"
                                               value="<?= $content_type->getCategoryTitleSlug() ?>" <?php

                                            if (in_array($content_type->getCategoryTitleSlug(), $vars['default_content_types'])) {
                                                echo 'checked="checked"';
                                            }

                                        ?>/>
                                        <label class="homepage_<?= $content_type->getCategoryTitleSlug() ?>"
                                               for="homepage_toggle_<?= $content_type->getCategoryTitleSlug() ?>">
                                            <?= $content_type->getIcon() ?>
                                            <?= $content_type->getCategoryTitle() ?>
                                        </label>
                                    </p>
                                </div>

                            <?php

                            }
                        }

                    ?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <?= \known\Core\site()->actions()->signForm('/account/settings/homepage') ?>

        </form>
    </div>

</div>