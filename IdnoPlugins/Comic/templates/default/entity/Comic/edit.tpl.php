<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span8 offset2 edit-pane">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <label>
                            Add a comic:<br />
                            <label>
                                <div id="photo-preview"></div>
                                    <span class="btn btn-primary btn-file">
                                        <i class="icon-camera"></i> <span id="photo-filename">Select a comic</span> <input type="file" name="comic" id="comic"
                                                                                                                           class="span9"
                                                                                                                           accept="image/*;capture=camera"
                                                                                                                           onchange="comicPreview(this)"/>

                                    </span>
                            </label>
                        </label>
                    <?php

                    }

                ?>
            </p>
            <p>
                <label>
                    Title (displayed in feeds)<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span9" />
                </label>
            </p>
            <p>
                <label>
                    Description of comic (displayed when image is not available)<br />
                    <textarea name="description" id="description" class="span9 bodyInput"><?=htmlspecialchars($vars['object']->description)?></textarea>
                </label>
            </p>
            <p>
                <label>
                    Accompanying text<br />
                    <textarea name="body" id="body" class="span9 bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('article'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/text/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
                <?= $this->draw('content/access'); ?>
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
                    $('#photo-filename').html('Choose different comic');
                    $('#photopreview').attr('src', e.target.result);
                    $('#photopreview').show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
<?=$this->draw('entity/edit/footer');?>