<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">
        
        	<h4>
				                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New RSVP<?php
                    } else {
                        ?>Edit RSVP<?php
                    }
                  ?>
			</h4>


            <div class="content-form">
                <label id="in-reply-to" for="reply">
                    What's the URL of the event you're responding to?</label>
                    <input type="text" id="reply" name="inreplyto" placeholder="The website address of the event" class="form-control" value="<?php if (empty($vars['url'])) { echo htmlspecialchars($vars['object']->inreplyto); } else { echo htmlspecialchars($vars['url']); } ?>" />
            </div>
            <div class="content-form">
                <label for="rsvp">
                    Are you going?</label>
                    <select class="form-control" name="rsvp" id="rsvp">
                        <option value="yes" <?php if ($vars['object']->rsvp == 'yes') echo "checked"; ?>>Yes :-)</option>
                        <option value="no" <?php if ($vars['object']->rsvp == 'no') echo "checked"; ?>>No :-(</option>
                        <option value="maybe" <?php if ($vars['object']->rsvp == 'maybe') echo "checked"; ?>>Maybe :-/</option>
                    </select>
            </div>

            <div class="content-form">
                <label for="body">
                    Any comments?</label>
                    <textarea name="body" id="body" class="form-control event" /><?=htmlspecialchars($vars['object']->body)?></textarea>
            </div>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?php echo $this->drawSyndication('note', $vars['object']->getPosseLinks()); ?>
            <?= $this->draw('content/access'); ?>
            <p class="button-bar">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/status/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Save" />

            </p>

        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>