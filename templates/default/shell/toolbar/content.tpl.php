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
                            $friendly_name = \Idno\Common\ContentType::categoryTitleSlugsToFriendlyName($vars['content']);
                        }

                        if (!empty($friendly_name)) {
                            echo $friendly_name;
                        } else {
                            echo 'Filter content';
                        }

                    ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <?php

                        echo $this->__(array( 'search' => $search ))->draw("shell/toolbar/content/default");
                        echo $this->__(array( 'search' => $search ))->draw("shell/toolbar/content/all");

                        foreach ($content_types as $content_type) {

                            if (empty($content_type->hide)) {
                                /* @var Idno\Common\ContentType $content_type */
                                echo $this->__(array( 'content_type' => $content_type, 'search' => $search ))->draw("shell/toolbar/content/type");
                            }
                        }

                    ?>


                </ul>
            </li>

    <?php

    }

?>
</ul>
