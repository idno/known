<?php echo $this->draw('entity/edit/header');?>
<form action="<?php echo $vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <h4>
                            <?php echo \Idno\Core\Idno::site()->language()->_('Upload a comic'); ?>
                            </h4>
                                <div id="photo-preview"></div>
                                    <span class="btn btn-primary btn-file">
                                        <i class="fa fa-upload"></i>
										<span id="photo-filename"><?php echo \Idno\Core\Idno::site()->language()->_('Select a comic'); ?></span> 
                                        <input type="file" name="comic" id="comic" class="form-control" accept="image/*" capture="camera" onchange="comicPreview(this)"/>

                                    </span>
                            </label>
                        </label>
                    <?php

                    }

                ?>
            </p>
            <div class="content-form">
                <label for="title">
                    <?php echo \Idno\Core\Idno::site()->language()->_('Title'); ?></label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($vars['object']->title)?>" class="form-control" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_('This is displayed in feeds'); ?>" />
            </div>
            <div class="content-form">
                <label for="description">
                    <?php echo \Idno\Core\Idno::site()->language()->_('Comic description'); ?></label>
                    <textarea name="description" id="description" class="form-control bodyInput" placeholder="<?php echo \Idno\Core\Idno::site()->language()->_("This is displayed when the image isn't available"); ?>"><?php echo htmlspecialchars($vars['object']->description)?></textarea>

            </div>
            <div class="content-form">
                <label for="body">
                    Accompanying text</label>
                    <textarea name="body" id="body" class="form-control comic bodyInput"><?php echo htmlspecialchars($vars['object']->body)?></textarea>

            </div>
            <?php echo $this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?php echo \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?php echo $this->drawSyndication('article', $vars['object']->getPosseLinks()); ?>
            <p>
                <?php echo \Idno\Core\Idno::site()->actions()->signForm('/text/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
                <?php echo $this->draw('content/extra'); ?>
                <?php echo $this->draw('content/access'); ?>
            </p>

        </div>

    </div>
</form>
    <script>
        function comicPreview(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#photo-preview').html('<img src="" id="photopreview" style="display:none; width: 400px">');
                    $('#photo-filename').html('<?php echo \Idno\Core\Idno::site()->language()->_('Choose different comic'); ?>');
                    $('#photopreview').attr('src', e.target.result);
                    $('#photopreview').show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
<?php echo $this->draw('entity/edit/footer'); ?>
