<?php

    $object = $vars['object'];

    if (!\Idno\Core\site()->session()->isLoggedOn() && $object instanceof \Idno\Common\Entity) {
        ?>
        <div class="row annotation-add">
            <div class="span1 owner h-card hidden-phone">
                <div class="u-url icon-container"><img class="u-photo"
                                                       src="<?= \Idno\Core\site()->config()->getDisplayURL() ?>gfx/users/default-00.png"/>
                </div>
            </div>
            <div class="span7 idno-comment-container">
                <form action="<?= \Idno\Core\site()->config()->getDisplayURL() ?>comments/post" method="post">
                    <input type="text" name="name" class="span7" placeholder="Your name" required>
                    <input type="text" name="url" class="span7" placeholder="Your website address">

                    <div id="extrafield" style="display:none"></div>
                    <textarea name="body" placeholder="Add a comment ..." class="span7 mentionable"></textarea>

                    <p style="text-align: right">
                        <?= \Idno\Core\site()->actions()->signForm('annotation/post') ?>
                        <input type="hidden" name="object" value="<?= $object->getUUID() ?>">
                        <input type="hidden" name="type" value="reply">
                        <input type="submit" class="btn btn-save" value="Leave Comment">
                    </p>
                </form>
            </div>
        </div>
        <script>
            $(document).ready(function () {

                $('#extrafield').html('<input type="hidden" name="validator" value="<?=$object->getUUID()?>">');

            })
        </script>
    <?php
    }
