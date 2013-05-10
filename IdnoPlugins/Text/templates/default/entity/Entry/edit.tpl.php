<form action="<?=Idno\Core\site()->config()->url?>Entry/edit/<?php if (!empty($vars['object'])) echo $vars['object']->getID()?>" method="post">

    <div class="row">

        <div class="span6">

            <p>
                <label>
                    Body<br />
                    <textarea name="body" class="span6 bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>
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
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>