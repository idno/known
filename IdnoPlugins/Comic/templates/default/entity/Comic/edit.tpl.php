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
                            <input type="file" name="comic" id="comic" class="span9" accept="image/*;capture=camera" />
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
<?=$this->draw('entity/edit/footer');?>