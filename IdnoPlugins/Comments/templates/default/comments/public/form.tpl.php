<?php

    $object = $vars['object'];

    $name_field = \Idno\Core\Bonita\Forms::obfuscateField('name');
    $url_field = \Idno\Core\Bonita\Forms::obfuscateField('url');

    $uuid = substr($object->getUUID(), -5);

if (!\Idno\Core\Idno::site()->session()->isLoggedOn() && $object instanceof \Idno\Common\Entity) {
    ?>
        <div class="row annotation-add">
            <div class="col-md-2 owner h-card hidden-sm hidden-xs">
                <div class="u-url icon-container"><img class="u-photo"
                                                       src="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>gfx/users/default-00.png"/>
                </div>
            </div>
            <div class="col-md-10 idno-comment-container" id="comment-form-<?= $uuid; ?>">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_("You probably shouldn't fill this in"); ?>">
                    <input type="url" name="url" class="form-control" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_("You probably shouldn't fill this in"); ?>">
                </div>
                <div class="form-group">
                    <input type="text" name="<?=$name_field?>" class="form-control" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('Your name'); ?>" required>
                </div>
                <div class="form-group">
                    <input type="url" name="<?=$url_field?>" class="form-control" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('Your website address'); ?>">
                </div>
                <div class="extrafield" style="display:none"></div>
                <div class="form-group">
                    <textarea name="body" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('Add a comment ...'); ?>" class="form-control mentionable"></textarea>
                </div>
                <p style="text-align: right" class="comment-submit">
                    <?php echo \Idno\Core\Idno::site()->actions()->signForm('annotation/post') ?>
                </p>
            </div>
        </div>
        <script>
            
            $('#comment-form-<?= $uuid; ?> .form-group:first-of-type').hide();
                
            $(document).ready(function () {
                
                setTimeout(function() {
                    $commentForm = $('#comment-form-<?=$uuid; ?>');
                    $commentForm.find('.extrafield').html('<input type="hidden" name="validator" value="<?php echo $object->getUUID()?>">');
                    $commentForm.html('<form action="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() ?>comments/post" method="post">' + $commentForm.html() + '</form>');
                    $commentForm.find('.comment-submit').append('<input type="hidden" name="object" value="<?php echo $object->getUUID() ?>"><input type="hidden" name="type" value="reply"><input type="submit" class="btn btn-save" value="<?php echo \Idno\Core\Idno::site()->language()->_('Leave Comment'); ?>">');
                },4000);

            })
        </script>
    <?php
}
