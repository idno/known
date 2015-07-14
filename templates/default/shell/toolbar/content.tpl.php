<ul class="nav navbar-nav">
<?php

    $content_types = \Idno\Common\ContentType::getRegistered();
    if (!empty($content_types)) {

        if (!empty($vars['subject'])) {
            $search = '?q=' . urlencode($vars['subject']);
        } else {
            $search = '';
        }

        ?>
        
            <li class="dropdown" tabindex="3">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    <?php

                        if (!empty($vars['content'])) {
                            echo \Idno\Common\ContentType::categoryTitleSlugsToFriendlyName($vars['content']);
                        } else {
                            echo 'Filter content';
                        }

                    ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() . $search ?>"><span class="dropdown-menu-icon">&nbsp;</span>
                            Default content</a></li>
                    <li><a href="<?= \Idno\Core\site()->config()->getDisplayURL() . 'content/all/' . $search ?>"><span class="dropdown-menu-icon">&nbsp;</span>
                            All content</a></li>
                    <?php

                        foreach ($content_types as $content_type) {

                            if (empty($content_type->hide)) {
                                /* @var Idno\Common\ContentType $content_type */
                                ?>
                                <li><a
                                    href="<?= \Idno\Core\site()->config()->getDisplayURL() ?>content/<?= $content_type->getCategoryTitleSlug() ?>/<?= $search ?>"><span
                                        class="dropdown-menu-icon"><?= $content_type->getIcon() ?></span> <?= $content_type->getCategoryTitle() ?>
                                </a></li><?php
                            }
                        }

                    ?>


                </ul>
            </li>

    <?php

    }

?>
</ul>