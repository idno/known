<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">
    	<div class="col-md-8 col-md-offset-2 edit-pane">
    	        			<h4>
				                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Event<?php
                    } else {
                        ?>Edit Event<?php
                    }
                  ?>
			</h4></div>

        <div class="col-md-8 col-md-offset-2">
        
            <div class="content-form">
                <label for="title">
                    Event name</label>
                    <?= $this->__([
                            'name' => 'title', 
                            'id' => 'title', 
                            'placeholder' => 'Give it a name', 
                            'value' => $vars['object']->title, 
                            'class' => 'form-control'])->draw('forms/input/input'); ?>

            </div>
        </div>
            
        <div class="col-md-4 col-md-offset-2">
            <div class="content-form">
                <label for="location">
                    Location</label>
                     <?= $this->__([
                            'name' => 'location', 
                            'id' => 'location', 
                            'placeholder' => 'Where will it take place?', 
                            'value' => $vars['object']->location, 
                            'class' => 'form-control'])->draw('forms/input/input'); ?>

            </div>
            <div class="content-form">
                <label for="starttime">
                    Start day and time</label>
                <?= $this->__([
                            'name' => 'starttime', 
                            'id' => 'starttime', 
                            'placeholder' => 'Type in the start day and time?', 
                            'value' => $vars['object']->starttime, 
                            'class' => 'form-control'])->draw('forms/input/datetime-local'); ?>
            </div>
            <div class="content-form">
                <label for="endtime">
                    End day and time</label>
                <?= $this->__([
                            'name' => 'endtime', 
                            'id' => 'endtime', 
                            'placeholder' => 'Type in the end day and time', 
                            'value' => $vars['object']->endtime, 
                            'class' => 'form-control'])->draw('forms/input/datetime-local'); ?>
                    
            </div>
            <?php echo $this->drawSyndication('event', $vars['object']->getPosseLinks()); ?>

        </div>
        <div class="col-md-4 ">
	        
	        <div class="content-form">
                <label for="summary">
                    Brief summary</label>
                    <?= $this->__([
                            'name' => 'summary', 
                            'id' => 'summary', 
                            'placeholder' => 'What\'s this about?', 
                            'value' => $vars['object']->summary, 
                            'class' => 'form-control'])->draw('forms/input/input'); ?>

            </div>

            <div class="content-form">
                <label for="body">
                    Description</label>
                    <?=$this->__([
                        'height' => '100', 'name' => 'body', 'value' => $vars['object']->body
                    ])->draw('forms/input/richtext');?>
            </div>
            <?=$this->draw('entity/tags/input');?>
            <?php if (empty($vars['object']->_id)) { 
                echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
            } ?>
        </div>

        <div class="col-md-8 col-md-offset-2">
            <?= $this->draw('content/extra'); ?>
	        <?= $this->draw('content/access'); ?>
            <p class="button-bar">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/event/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Save" />

            </p>
        </div>
    </div>
</form>
<script>
    autoSave('event', ['title','summary','location','starttime','endtime','body']);
</script>
<?=$this->draw('entity/edit/footer');?>