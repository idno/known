<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <label>
                    Address of the page to bookmark:<br />
                    <input required type="url" name="body" id="body" value="<?php if (empty($vars['url'])) { echo htmlspecialchars($vars['object']->body); } else { echo htmlspecialchars($vars['url']); } ?>" class="span9" />
                </label>
                <label>
                    If you want, enter some tags or a note here:<br />
                    <input type="text" name="description" id="description" value="<?=htmlspecialchars($vars['object']->description)?>" class="span9" />
                </label>
                <label>
                    Tags<br />
                    <input type="text" name="description" id="description" value="<?=htmlspecialchars($vars['object']->description)?>" class="span9" />
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('bookmark'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/like/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>