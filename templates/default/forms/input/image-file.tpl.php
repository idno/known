<?php

    if (empty($vars['id']))
        $vars['id'] = 'photo-' . md5(rand());
    
    $multiple = false;
    if (strpos($vars['name'], '[]') !== false)
        $multiple = true;
?>
<div class="image-file-input">
    <div class="photo-preview-existing">
        <?php 
        if (!empty($vars['object']->_id)) {

            $attachments = $vars['object']->getAttachments(); // TODO: Handle multiple
            $attachment = $attachments[0];
            $filename = $attachment['filename'];

            $mainsrc = $attachment['url'];
            if (!empty($vars['object']->thumbs_large) && !empty($vars['object']->thumbs_large[$filename])) {
                $src = $vars['object']->thumbs_large[$filename]['url'];

                // Old style
            } else if (!empty($vars['object']->thumbnail_large)) {
                $src = $vars['object']->thumbnail_large;

                // Really old style
            } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                $src = $vars['object']->thumbnail;

                // Fallback
            } else {
                $src = $mainsrc;
            }

            // Patch to correct certain broken URLs caused by https://github.com/idno/known/issues/526
            $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $src);
            $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $mainsrc);

            $src = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($src);
            $mainsrc = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($mainsrc);
            ?>
            <img src="<?= $this->makeDisplayURL($src) ?>" class="existing"/>
        <?php     
        }
        ?>
    </div>
    <div class="photo-preview" id="<?= $vars['id']; ?>_preview">
        <img id="<?= $vars['id']; ?>_img" src="" class="preview" style="display:none; width: 400px;" />
    </div>
    <p>
        <span class="btn btn-primary btn-file">
            <i class="fa fa-camera"></i> 
            <span class="photo-filename" data-nexttext="<?= \Idno\Core\Idno::site()->language()->_('Choose different photo'); ?>">
                <?php 
                    if (empty($vars['object']->_id)) { 
                        echo \Idno\Core\Idno::site()->language()->_('Select a photo'); 
                    } else { 
                        if (!$multiple)
                            echo \Idno\Core\Idno::site()->language()->_('Choose different photo'); 
                        else
                            echo \Idno\Core\Idno::site()->language()->_('Add photo'); 
                    } 
                ?>
            </span> 
            <?=
            $this->__([
                'name' => $vars['name'],
                'id' => $vars['id'],
                'accept' => 'image/*',
                'onchange' => 'Template.activateImagePreview(this)',
                'class' => 'input-file form-control col-md-9'])->draw('forms/input/file');
            ?>
        </span>
    </p>
</div>