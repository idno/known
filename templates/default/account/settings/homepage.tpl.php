<?php
    $user = \Idno\Core\site()->session()->currentUser();
?>
<div class="row">

    <div class="span10 offset1">
        <h1>
            Homepage content
        </h1>
        <?= $this->draw('account/menu') ?>
        <div class="explanation">
            <p>
                Choose what content people will see by default when they visit your site.
                Visitors can always use the content menu to find types of content that you don't show on the main page.
            </p>
        </div>

		<div class="control-label">Select the content types that you want to display on the main page.
                    </div>
                    <br>
        <form action="<?= \Idno\Core\site()->config()->url ?>account/settings/homepage" method="post"
              class="form-horizontal" enctype="multipart/form-data">

            <div class="control-group">
                <!--<div class="control-label">Content to display<br/>
                    <small>Select the content types that you want to display on the main page.
                    </small>
                </div>-->
                <div class="">
                    <?php

                        if (!empty($vars['content_types'])) {
                            foreach ($vars['content_types'] as $content_type) {
                                /* @var \Idno\Common\ContentType $content_type */
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
                <div class="">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            <?= \Idno\Core\site()->actions()->signForm('/account/settings/homepage') ?>

        </form>
    </div>

</div>