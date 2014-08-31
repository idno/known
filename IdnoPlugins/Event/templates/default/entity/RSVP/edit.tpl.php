<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span8 offset2 edit-pane">
        
        	<h4>
				                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New RSVP<?php
                    } else {
                        ?>Edit RSVP<?php
                    }
                  ?>
			</h4>

            <p>
                <span id="in-reply-to">
                    What's the URL of the event you're responding to?<br />
                    <input type="text" name="inreplyto" placeholder="The website address of the event" class="span8" value="<?php if (empty($vars['url'])) { echo htmlspecialchars($vars['object']->inreplyto); } else { echo htmlspecialchars($vars['url']); } ?>" />
                </span>
            </p>
            <p>
                <label>
                    Are you going?<br />
                    <select name="rsvp">
                        <option value="yes" <?php if ($vars['object']->rsvp == 'yes') echo "checked"; ?>>Yes :-)</option>
                        <option value="no" <?php if ($vars['object']->rsvp == 'no') echo "checked"; ?>>No :-(</option>
                        <option value="maybe" <?php if ($vars['object']->rsvp == 'maybe') echo "checked"; ?>>Maybe :-/</option>
                    </select>
                </label>
            </p>
            <p>
                <label>
                    Any comments?<br />
                    <input type="text" name="body" id="body" value="<?=htmlspecialchars($vars['object']->body)?>" class="span8" />
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('note'); ?>
            <p class="button-bar">
                <?= \Idno\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Save" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>