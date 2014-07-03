<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span8 offset2">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                ?>
                <label>
                    <span class="btn btn-primary btn-file">
                        <i class="icon-camera"></i> <span id="photo-filename">Take a photo</span> <input type="file" name="photo" id="photo" class="span9" accept="image/*;capture=camera" onchange="photoPreview(this)" />
                    </span>
                </label>
                <?php

                    }

                ?>
            </p>
            <p>
                <label>
                    Title:<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span8" />
                </label>
            </p>
            <p>
                <label>
                    Description<br />
                    <textarea name="body" id="body" class="span8 bodyInput mentionable"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('image'); ?>
            <p class="button-bar ">
                <?= \Idno\Core\site()->actions()->signForm('/photo/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Publish" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<script>
    if (typeof photoPreview !== function) {
        function photoPreview(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#photo-filename').html('<img src="" id="photopreview" style="display:none">');
                    $('#photopreview').attr('src', e.target.result);
                    $('#photopreview').show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    }
</script>
<?=$this->draw('entity/edit/footer');?>