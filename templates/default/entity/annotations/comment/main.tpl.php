<?php

    $user = \Idno\Core\site()->session()->currentUser();
    $object = $vars['object'];

    if (!empty($user) && !empty($object)) {

?>
    <div class="row annotation-add">
        <div class="span1 owner h-card hidden-phone">
            <a href="<?= $user->getDisplayURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                src="<?= $user->getIcon() ?>"/></a>
        </div>
        <div class="span7 idno-comment-container">
            <form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>annotation/post" method="post">
                <textarea name="body" placeholder="Add a comment ..." class="span7 mentionable"></textarea>
                <p style="text-align: right">
                    <?= \Idno\Core\site()->actions()->signForm('annotation/post') ?>
                    <input type="hidden" name="object" value="<?=$object->getUUID()?>">
                    <input type="hidden" name="type" value="reply">
                    <input type="submit" class="btn btn-save" value="Leave Comment">
                </p>
            </form>
        </div>
    </div>
<?php

    // Prevent scope pollution
    unset($this->vars['action']);
    }

?>