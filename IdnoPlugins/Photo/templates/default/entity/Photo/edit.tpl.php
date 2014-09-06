<?= $this->draw('entity/edit/header'); ?>
    <form action="<?= $vars['object']->getURL() ?>" method="post" enctype="multipart/form-data">

        <div class="row">

            <div class="span8 offset2 edit-pane">

                <h4>
                    <?php

                        if (empty($vars['object']->_id)) {
                            ?>New Photo<?php
                        } else {
                            ?>Edit Photo<?php
                        }

                    ?>
                </h4>

                <p>
                    <?php

                        if (empty($vars['object']->_id)) {

                            ?>
                            <label>
                                <div id="photo-preview"></div>
                                    <span class="btn btn-primary btn-file">
                                        <i class="icon-camera"></i> <span id="photo-filename">Select a photo</span> <input type="file" name="photo" id="photo"
                                                                                    class="span9"
                                                                                    accept="image/*;capture=camera"
                                                                                    onchange="photoPreview(this)"/>

                                    </span>
                            </label>
                        <?php

                        }

                    ?>
                </p>
                <p>
                    <label>
                        Title<br/>
                        <input type="text" name="title" id="title"
                               value="<?= htmlspecialchars($vars['object']->title) ?>" class="span8"
                               placeholder="Give it a title"/>
                    </label>
                </p>

                <p>
                    <label>
                        Description<br/>
                        <textarea name="body" id="description" class="span8 bodyInputShort mentionable"
                                  placeholder="Add a caption"><?= htmlspecialchars($vars['object']->body) ?></textarea>
                    </label>
                    <label>
                        Tags<br/>
                        <input type="text" name="tags" id="tags" placeholder="Add some #tags"
                               value="<?= htmlspecialchars($vars['object']->tags) ?>" class="span8"/>
                    </label>
                </p>
                <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('image'); ?>
                <p class="button-bar ">
                    <?= \Idno\Core\site()->actions()->signForm('/photo/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>
                    <?= $this->draw('content/access'); ?>
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