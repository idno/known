<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span4 offset1">
            <p>
                <label>
                    Event name<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Brief summary of what you're going to do<br />
                    <input type="text" name="summary" id="summary" value="<?=htmlspecialchars($vars['object']->summary)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Location<br />
                    <input type="text" name="location" id="location" value="<?=htmlspecialchars($vars['object']->location)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    Start day and time<br />
                    <input type="text" name="starttime" id="starttime" value="<?=htmlspecialchars($vars['object']->starttime)?>" class="span4" />
                </label>
            </p>
            <p>
                <label>
                    End day and time<br />
                    <input type="text" name="endtime" id="endtime" value="<?=htmlspecialchars($vars['object']->endtime)?>" class="span4" />
                </label>
            </p>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/event/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>
        <div class="span6 ">

            <p>
                <label>
                    Body<br />
                    <textarea name="body" id="body" class="span6 bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>

        </div>

    </div>
</form>