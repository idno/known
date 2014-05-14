<?php

    $content_types = \known\Common\ContentType::getRegistered();
    if (!empty($content_types)) {

        if (!empty($vars['subject'])) {
            $search = '?q=' . urlencode($vars['subject']);
        } else {
            $search = '';
        }

        ?>

        <ul class="nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php

                        if (!empty($vars['content'])) {
                            echo \known\Common\ContentType::categoryTitleSlugsToFriendlyName($vars['content']);
                        } else {
                            echo 'Default content';
                        }

                    ?>
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?= \known\Core\site()->config()->url . $search ?>"><span class="dropdown-menu-icon">&nbsp;</span>
                            Default content</a></li>
                    <li><a href="<?= \known\Core\site()->config()->url . 'content/all/' . $search ?>"><span class="dropdown-menu-icon">&nbsp;</span>
                            All content</a></li>
                    <?php

                        foreach ($content_types as $content_type) {

                            if (empty($content_type->hide)) {
                                /* @var known\Common\ContentType $content_type */
                                ?>
                                <li><a
                                    href="<?= \known\Core\site()->config()->url ?>content/<?= $content_type->getCategoryTitleSlug() ?>/<?= $search ?>"><span
                                        class="dropdown-menu-icon"><?= $content_type->getIcon() ?></span> <?= $content_type->getCategoryTitle() ?>
                                </a></li><?php
                            }
                        }

                    ?>
                </ul>
            </li>
        </ul>

    <?php

    }

?>