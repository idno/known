<?php

    $user = \Idno\Core\site()->session()->currentUser();
    $object = $vars['object'];

    if (!empty($user) && !empty($object)) {

        ?>
        <div class="row annotation-add-mini">
            <div class="span1 owner h-card hidden-phone">
                <a href="<?= $user->getURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                   src="<?= $user->getIcon() ?>"/></a>
            </div>
            <div class="span7 idno-comment-container-mini">
                <form action="<?=\Idno\Core\site()->config()->getURL()?>annotation/post" method="post">
                    <input type="text" name="body" placeholder="Add a comment ..." class="span7 mentionable">
                    <?= \Idno\Core\site()->actions()->signForm('annotation/post') ?>
                    <input type="hidden" name="object" value="<?=$object->getUUID()?>">
                    <input type="hidden" name="type" value="reply">
                    <input type="submit" class="btn btn-save" value="Leave Comment" style="display: none">
                </form>
            </div>
        </div>
    <?php

    }

?>