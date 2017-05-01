<?= $this->draw('entity/edit/header'); ?>
    <form action="<?= $vars['object']->getURL() ?>" method="post" enctype="multipart/form-data">

        <div class="row">

            <div class="col-md-8 col-md-offset-2 edit-pane">

                <h4>
                    <?php

                        if (empty($vars['object']->_id)) {
                            ?>New Photo<?php
                        } else {
                            ?>Edit Photo<?php
                        }

                    ?>
                </h4>

                <?php

                   // if (empty($vars['object']->_id)) {

                        ?>
                        <div id="photo-preview"><?php if (!empty($vars['object']->_id)) {
                            
                            $attachments = $vars['object']->getAttachments(); // TODO: Handle multiple
                            $attachment = $attachments[0];
                        
                            $mainsrc = $attachment['url'];
                            if (!empty($vars['object']->thumbnail_large)) {
                                $src = $vars['object']->thumbnail_large;
                            } else if (!empty($vars['object']->thumbnail)) { // Backwards compatibility
                                $src = $vars['object']->thumbnail;
                            } else {
                                $src = $mainsrc;
                            }

                            // Patch to correct certain broken URLs caused by https://github.com/idno/known/issues/526
                            $src = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $src);
                            $mainsrc = preg_replace('/^(https?:\/\/\/)/', \Idno\Core\Idno::site()->config()->getDisplayURL(), $mainsrc);

                            $src = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($src);
                            $mainsrc = \Idno\Core\Idno::site()->config()->sanitizeAttachmentURL($mainsrc);
                            
                        
                            ?><img src="<?=  $this->makeDisplayURL($src) ?>" id="photopreview"><?php
                        } ?></div>
                        <p>
                                <span class="btn btn-primary btn-file">
                                        <i class="fa fa-camera"></i> <span
                                        id="photo-filename"><?php if (empty($vars['object']->_id)) { ?>Select a photo<?php } else { ?>Choose different photo<?php } ?></span> <input type="file" name="photo"
                                                                                         id="photo"
                                                                                         class="col-md-9 form-control"
                                                                                         accept="image/*"
                                                                                         <?php /* capture="camera" */ ?>
                                                                                         onchange="photoPreview(this)"/>

                                    </span>
                        </p>

                    <?php

                  //  }

                ?>

                <div id="photo-details" style="<?php

                    /*if (empty($vars['object']->_id)) {
                        echo 'display:none';
                    }*/

                    ?>">

                    <div class="content-form">
                        <label for="title">
                            Title</label>
                        <input type="text" name="title" id="title"
                               value="<?= htmlspecialchars($vars['object']->title) ?>" class="form-control"
                               placeholder="Give it a title"/>
                    </div>

                    <?= $this->__([
                        'name' => 'body',
                        'value' => $vars['object']->body,
                        'wordcount' => false,
                        'class' => 'wysiwyg-short',
                        'height' => 100,
                        'placeholder' => 'Describe your photo',
                        'label' => 'Description'
                    ])->draw('forms/input/richtext')?>

                    <?= $this->draw('entity/tags/input'); ?>

                </div>
                <div id="photo-details-toggle" style="<?php
                    //if (!empty($vars['object']->_id)) {
                        echo 'display:none';
                    //}
                ?>">
                    <p>
                        <small><a href="#" onclick="$('#photo-details').show(); $('#photo-details-toggle').hide(); return false;">+ Add details</a></small>
                    </p>
                </div>
                
                <?php echo $this->drawSyndication('image', $vars['object']->getPosseLinks()); ?>
                <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to"
                                                                  value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
                <?= $this->draw('content/access'); ?>
                <p class="button-bar ">
                    <?= \Idno\Core\Idno::site()->actions()->signForm('/photo/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>
                </p>
            </div>

        </div>
    </form>
    <script>
        //if (typeof photoPreview !== function) {
        function photoPreview(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#photo-preview').html('<img src="" id="photopreview" style="display:none; width: 400px">');
                    $('#photo-filename').html('Choose different photo');
                    $('#photopreview').attr('src', e.target.result);
                    $('#photopreview').show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        //}
    </script>
<?= $this->draw('entity/edit/footer'); ?>