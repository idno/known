<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <span id="in-reply-to">
                    What's the web address of the event you're replying to?<br />
                    <input type="text" name="inreplyto" placeholder="The website address of the event" class="span9" value="<?=htmlspecialchars($vars['object']->inreplyto)?>" />
                </span>
            </p>
            <p>
                <label>
                    Are you going?<br />
                    <select name="rsvp">
                        <option value="yes" <?php if ($vars['object']->rsvp == 'yes') echo "checked"; ?>>Yes!</option>
                        <option value="no" <?php if ($vars['object']->rsvp == 'no') echo "checked"; ?>>No!</option>
                        <option value="maybe" <?php if ($vars['object']->rsvp == 'maybe') echo "checked"; ?>>Maybe!</option>
                    </select>
                </label>
            </p>
            <p>
                <label>
                    Any comments?<br />
                    <input type="text" name="body" id="body" value="<?=htmlspecialchars($vars['object']->body)?>" class="span9" />
                </label>
                <?= \Idno\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save" />
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();" />
            </p>
        </div>

    </div>
</form>