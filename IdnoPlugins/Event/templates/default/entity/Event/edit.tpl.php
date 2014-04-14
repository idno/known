<?php

    $autosave = new \Idno\Core\Autosave();
    if (!($title = $vars['object']->title)) {
        $title = $autosave->getValue('event','title');
    }
    if (!($summary = $vars['object']->summary)) {
        $summary = $autosave->getValue('event','summary');
    }
    if (!($location = $vars['object']->location)) {
        $location = $autosave->getValue('event','location');
    }
    if (!($starttime = $vars['object']->starttime)) {
        $starttime = $autosave->getValue('event','starttime');
    }
    if (!($endtime = $vars['object']->endtime)) {
        $endtime = $autosave->getValue('event','endtime');
    }
    if (!($body = $vars['object']->body)) {
        $body = $autosave->getValue('event','body');
    }

?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span4 offset1">
            <p>
                <label>
                    Event name<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($title)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Brief summary of what you're going to do<br />
                    <input type="text" name="summary" id="summary" value="<?=htmlspecialchars($summary)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Location<br />
                    <input type="text" name="location" id="location" value="<?=htmlspecialchars($location)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Start day and time<br />
                    <input type="text" name="starttime" id="starttime" value="<?=htmlspecialchars($starttime)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    End day and time<br />
                    <input type="text" name="endtime" id="endtime" value="<?=htmlspecialchars($endtime)?>" class="span4" />
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('event'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/event/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>
        <div class="span6 ">

            <p>
                <label>
                    Body<br />
                    <textarea name="body" id="body" class="span6 bodyInput" required><?=htmlspecialchars($body)?></textarea>
                </label>
            </p>

        </div>

    </div>
</form>
<script>
    autoSave('event', ['title','summary','location','starttime','endtime','body']);
</script>