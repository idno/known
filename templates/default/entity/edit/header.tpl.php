<?php

    if ($page = \Idno\Core\site()->currentPage()) {
        if ($page->getInput('sharing')) {

            $share_type = $page->getInput('share_type');
            if (empty($share_type)) {
                $share_type = 'note';
            }

?>

            <div class="row">
                <div class="span10 offset1">
                    <ul class="nav nav-tabs">
                        <?php

                            foreach(['note' => 'Share', 'reply' => 'Reply', 'like' => 'Bookmark', 'rsvp' => 'RSVP'] as $variable => $label) {

                                if ($content_type = \Idno\Common\ContentType::getRegisteredForIndieWebPostType($variable)) {

                                    ?>
                                    <li <?php if ($variable == $share_type) { ?>class="active"<?php } ?>>
                                        <a href="<?=$this->getURLWithVar('share_type', $variable);?>"><?=$label?></a>
                                    </li>
                                    <?php

                                }

                            }

                        ?>
                    </ul>
                </div>
            </div>

<?php

        }
    }

?>