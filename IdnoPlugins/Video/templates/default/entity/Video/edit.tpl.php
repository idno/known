<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                ?>
                <label>
                    Take a video:<br />
                    <input type="file" name="video" id="video" class="span9" accept="video/*;capture=camcorder" />
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
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/video/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>