<?php

    if ($page = \Idno\Core\Idno::site()->currentPage()) {
        if ($page->getInput('sharing')) {

            $share_type = $page->getInput('share_type');
            if (empty($share_type)) {
                $share_type = 'note';
            }

?>

            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <ul class="nav nav-tabs">
                        <?php

                            $postTypes = array('note' => 'Share', 'reply' => 'Reply', 'bookmark' => 'Bookmark', 'rsvp' => 'RSVP');
                            $postTypes = \Idno\Core\Idno::site()->triggerEvent('share/types', ['types' => $postTypes], $postTypes);

                            foreach($postTypes as $variable => $label) {

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