<?php

    $autosave = new \Idno\Core\Autosave();
    if (!empty($vars['object']->body)) {
        $body = $vars['object']->body;
    } else {
        $body = $autosave->getValue('entry','body');
    }
    if (!empty($vars['object']->title)) {
        $title = $vars['object']->title;
    } else {
        $title = $autosave->getValue('entry','title');
    }

?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span6 offset1">

            <p>
                <label>
                    Body<br />
                    <textarea required name="body" id="body" class="span6 bodyInput"><?=htmlspecialchars($body)?></textarea>
                </label>
            </p>

        </div>

        <div class="span4">
            <p>
                <label>
                    Title<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($title)?>" class="span4" />
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
<script>
    // Autosave the title & body
    autoSave('entry', 'body');
    autoSave('entry', 'title');
</script>