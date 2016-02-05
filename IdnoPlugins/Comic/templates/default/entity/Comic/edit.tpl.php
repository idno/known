<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <h4>
                            Upload a comic
                            </h4>
                                <div id="photo-preview"></div>
                                    <span class="btn btn-primary btn-file">
                                        <i class="fa fa-upload"></i>
										<span id="photo-filename">Select a comic</span> 
                                        <input type="file" name="comic" id="comic" class="form-control" accept="image/*;capture=camera" onchange="comicPreview(this)"/>

                                    </span>
                            </label>
                        </label>
                    <?php

                    }

                ?>
            </p>
            <div class="content-form">
                <label for="title">
                    Title</label>
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="form-control" placeholder="This is displayed in feeds" />
            </div>
            <div class="content-form">
                <label for="description">
                    Comic description</label>
                    <textarea name="description" id="description" class="form-control bodyInput" placeholder="This is displayed when the image isn't available"><?=htmlspecialchars($vars['object']->description)?></textarea>

            </div>
            <div class="content-form">
                <label for="body">
                    Accompanying text</label>
                    <textarea name="body" id="body" class="form-control comic bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>

            </div>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?php echo $this->drawSyndication('article', $vars['object']->getPosseLinks()); ?>
            <p>
                <?= \Idno\Core\Idno::site()->actions()->signForm('/text/edit') ?>
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