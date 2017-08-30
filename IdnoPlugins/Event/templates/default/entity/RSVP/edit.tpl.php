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
                    <?php 
                        $value = "";
                        if (empty($vars['url'])) { 
                            $value = $vars['object']->inreplyto;
                        } else { 
                            $value = $vars['url']; 
                        } ?>
                    <?= $this->__([
                            'name' => 'inreplyto', 
                            'id' => 'reply', 
                            'placeholder' => 'The website address of the event', 
                            'value' => $value, 
                            'class' => 'form-control'])->draw('forms/input/url'); ?>
            </div>
            <div class="content-form">
                <label for="rsvp">
                    Are you going?</label>
                    <select class="form-control" name="rsvp" id="rsvp">
                        <option value="yes" <?php if ($vars['object']->rsvp == 'yes') echo "checked"; ?>>Yes: I am attending this event</option>
                        <option value="no" <?php if ($vars['object']->rsvp == 'no') echo "checked"; ?>>No: I am not attending this event</option>
                        <option value="maybe" <?php if ($vars['object']->rsvp == 'maybe') echo "checked"; ?>>Maybe: I might attend this event</option>
                    </select>
                    <?php
                    // Not got a select control just yet, lets just publish for now
                    $this->documentFormControl('rsvp', [
                        'description' => 'Are you going?',
                        'id' => 'rsvp',
                        'type' => 'select'
                    ]);
                    ?>
            </div>

            <div class="content-form">
                <label for="body">
                    Any comments?</label>
                    <?= $this->__([
                        'name' => 'body', 
                        'id' => 'body', 
                        'placeholder' => 'Say something here...', 
                        'height' => 126,
                        'value' => $vars['object']->body, 
                        'class' => 'form-control event'])->draw('forms/input/longtext'); ?>
            </div>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) { 
                echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
            } ?>
            <?php echo $this->drawSyndication('note', $vars['object']->getPosseLinks()); ?>
            <?= $this->draw('content/extra'); ?>
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