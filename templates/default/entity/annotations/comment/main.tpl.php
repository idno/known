<?php

    $user = \Idno\Core\Idno::site()->session()->currentUser();
    $object = $vars['object'];

    if (!empty($user) && !empty($object)) {

?>
    <div class="row annotation-add">
        <div class="col-md-2 owner h-card visible-md visible-lg">
            <a href="<?= $user->getDisplayURL() ?>" class="u-url icon-container"><img class="u-photo"
                                                                                      src="<?= $user->getIcon() ?>"/></a>
        </div>
        <div class="col-md-10 idno-comment-container">
            <form action="<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>annotation/post" method="post">
                <textarea name="body" placeholder="Add a comment ..." class="form-control mentionable"></textarea>
                <p style="text-align: right">
                    <?= \Idno\Core\Idno::site()->actions()->signForm('annotation/post') ?>
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