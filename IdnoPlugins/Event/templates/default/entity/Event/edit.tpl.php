<?=$this->draw('entity/edit/header');?>
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
    	<div class="span8 offset2 edit-pane">
    	        			<h4>
				                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Event<?php
                    } else {
                        ?>Edit Event<?php
                    }
                  ?>
			</h4></div>

        <div class="span4 offset2">
        
            <p>
                <label>
                    Event name<br />
                    <input type="text" name="title" id="title" placeholder="Give it a name" value="<?=htmlspecialchars($title)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Brief summary<br />
                    <input type="text" name="summary" id="summary" placeholder="What's this about?" value="<?=htmlspecialchars($summary)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Location<br />
                    <input type="text" name="location" id="location" placeholder="Where will it take place?" value="<?=htmlspecialchars($location)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Start day and time<br />
                    <input type="text" name="starttime" id="starttime" placeholder="Type in the start day and time" value="<?=htmlspecialchars($starttime)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    End day and time<br />
                    <input type="text" name="endtime" id="endtime" placeholder="Type in the end day and time" value="<?=htmlspecialchars($endtime)?>" class="span4" />
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('event'); ?>

        </div>
        <div class="span4 ">

            <p>
                <label>
                    Description<br />
                    <textarea name="body" id="body" class="span4 bodyInput mentionable" placeholder="Describe the event" required><?=htmlspecialchars($body)?></textarea>
                </label>
            </p>

        </div>
        
        <div class="span8 offset2">
            <p class="button-bar">
                <?= \Idno\Core\site()->actions()->signForm('/event/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Save" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>
    </div>
</form>
<script>
    autoSave('event', ['title','summary','location','starttime','endtime','body']);
</script>
<?=$this->draw('entity/edit/footer');?>