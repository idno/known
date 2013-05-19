<form action="<?=$vars['object']->getEditURL()?>" method="post">

    <div class="row">

        <div class="span6">

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
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/text/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" onclick="localstorage.removeItem('Text')" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>