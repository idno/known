<?php

    $object = $vars['object'];

    if (!\Idno\Core\site()->session()->isLoggedOn() && $object instanceof \Idno\Common\Entity) {
        ?>
        <div class="row annotation-add">
            <div class="col-md-1 owner h-card hidden-sm">
                <div class="u-url icon-container"><img class="u-photo"
                                                       src="<?= \Idno\Core\site()->config()->getDisplayURL() ?>gfx/users/default-00.png"/>
                </div>
            </div>
            <div class="col-md-11 idno-comment-container" id="comment-form">
                <input type="text" name="name" class="form-control" placeholder="Your name" required>
                <input type="text" name="url" class="form-control" placeholder="Your website address">
                <div id="extrafield" style="display:none"></div>
                <textarea name="body" placeholder="Add a comment ..." class="form-control mentionable"></textarea>

                <p style="text-align: right" id="comment-submit">
                    <?= \Idno\Core\site()->actions()->signForm('annotation/post') ?>
                </p>
            </div>
        </div>
        <script>
            $(document).ready(function () {

                setTimeout(function() {
                    $('#extrafield').html('<input type="hidden" name="validator" value="<?=$object->getUUID()?>">');
                    //$('#comment-form').prepend('<form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>comments/post" method="post">');
                    //$('#comment-form').append('</form>');
                    $('#comment-form').html('<form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>comments/post" method="post">' + $('#comment-form').html() + '</form>');
                    $('#comment-submit').append('<input type="hidden" name="object" value="<?= $object->getUUID() ?>"><input type="hidden" name="type" value="reply"><input type="submit" class="btn btn-save" value="Leave Comment">');
                },4000);

            })
        </script>
    <?php
    }
