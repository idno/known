<form action="<?=$vars['object']->getEditURL()?>" method="post">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <label>
                    What are you up to?<br />
                    <input type="text" name="body" id="body" value="<?=htmlspecialchars($vars['object']->body)?>" class="span9" />
                </label>
                <?= \Idno\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>