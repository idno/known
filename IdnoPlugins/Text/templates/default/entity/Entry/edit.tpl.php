<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span6 offset1">

            <p>
                <label>
                    Body<br />
                    <textarea name="body" id="body" class="span6 bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>

        </div>

        <div class="span4">
            <p>
                <label>
                    Title<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span4" />
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