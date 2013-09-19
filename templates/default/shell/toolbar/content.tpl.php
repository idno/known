<?php

    $content_types = \Idno\Common\ContentType::getRegistered();
    if (!empty($content_types)) {

?>

        <ul class="nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    Content
                    <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?=\Idno\Core\site()->config()->url?>"><span class="dropdown-menu-icon">&nbsp;</span> Everything</a></li>
                    <?php

                        foreach($content_types as $content_type) {

                            if (empty($content_type->hide)) {
                                /* @var Idno\Common\ContentType $content_type */
                                ?><li><a href="<?=\Idno\Core\site()->config()->url?>content/<?=urlencode(strtolower(str_replace(' ','',$content_type->getCategoryTitle())))?>"><span class="dropdown-menu-icon" ><?= $content_type->getIcon() ?></span> <?=$content_type->getCategoryTitle()?></a></li><?php
                            }
                        }

                    ?>
                </ul>
            </li>
        </ul>

<?php

    }

?>