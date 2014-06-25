<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                ?>
                <label>
                    Take a video:<br />
                    <span class="btn btn-primary btn-file">
                        <i class="icon-facetime-video"></i> <span id="video-filename">Take a video</span> <input type="file" name="video" id="video" class="span9" accept="video/*;capture=camcorder" onchange="$('#video-filename').html($(this).val())" />
                    </span>
                </label>
                <?php

                    }

                ?>
            </p>
            <p>
                <label>
                    Title:<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span9" />
                </label>
            </p>
            <p>
                <label>
                    Description<br />
                    <textarea name="body" id="body" class="span9 bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('video'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/video/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>