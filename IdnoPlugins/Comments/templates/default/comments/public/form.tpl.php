<?php

    $object = $vars['object'];

    if (!\Idno\Core\Idno::site()->session()->isLoggedOn() && $object instanceof \Idno\Common\Entity) {
        ?>
        <div class="row annotation-add">
            <div class="col-md-2 owner h-card hidden-sm hidden-xs">
                <div class="u-url icon-container"><img class="u-photo"
                                                       src="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>gfx/users/default-00.png"/>
                </div>
            </div>
            <div class="col-md-10 idno-comment-container" id="comment-form">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Your name" required>
                </div>
                <div class="form-group">
                    <input type="text" name="url" class="form-control" placeholder="Your website address">
                </div>
                <div class="form-group">
                    <input type="text" name="url-2" class="form-control" placeholder="You probably shouldn't fill this in" style="display: none;" >
                </div>
                <div id="extrafield" style="display:none"></div>
                <div class="form-group">
                    <textarea name="body" placeholder="Add a comment ..." class="form-control mentionable"></textarea>
                </div>
                <p style="text-align: right" id="comment-submit">
                    <?= \Idno\Core\Idno::site()->actions()->signForm('annotation/post') ?>
                </p>
            </div>
        </div>
        <script>
            $(document).ready(function () {

                setTimeout(function() {
                    $('#extrafield').html('<input type="hidden" name="validator" value="<?=$object->getUUID()?>">');
                    //$('#comment-form').prepend('<form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>comments/post" method="post">');
                    //$('#comment-form').append('</form>');
                    $('#comment-form').html('<form action="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() ?>comments/post" method="post">' + $('#comment-form').html() + '</form>');
                    $('#comment-submit').append('<input type="hidden" name="object" value="<?= $object->getUUID() ?>"><input type="hidden" name="type" value="reply"><input type="submit" class="btn btn-save" value="Leave Comment">');
                },4000);

            })
        </script>
    <?php
    }
