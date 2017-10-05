<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">
        
        	<h4>
        	
        	                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Audio<?php
                    } else {
                        ?>Edit Audio<?php
                    }

                ?>
        	</h4>

            <p>
                
                <label>
                    <span class="btn btn-primary btn-file">
                        <i class="fa fa-play-circle"></i> <span id="media-filename"><?php if (empty($vars['object']->_id)) { ?>Upload audio<?php } else { ?>Choose different audio<?php } ?></span> 
                        <?= $this->__([
                                            'name' => 'media', 
                                            'id' => 'media', 
                                            'accept' => 'audio/*;video/*;capture=microphone',
                                            'onchange' => "$('#media-filename').html($(this).val())",
                                            'class' => 'col-md-9'])->draw('forms/input/file'); ?>
                    </span>
                </label>
                
            </p>
            <div class="content-form">
                <label for="title">
                    Title</label>
                    <?= $this->__([
                            'name' => 'title', 
                            'id' => 'title', 
                            'placeholder' => 'Give it a title', 
                            'value' => $vars['object']->title, 
                            'class' => 'form-control'])->draw('forms/input/input'); ?>

            </div>

            <?= $this->__([
                'name' => 'body',
                'value' => $vars['object']->body,
                'wordcount' => false,
                'height' => 250,
                'class' => 'wysiwyg-short',
                'placeholder' => 'Describe your audio',
                'label' => 'Description',
            ])->draw('forms/input/richtext')?>
            <?=$this->draw('entity/tags/input');?>
            <?php echo $this->drawSyndication('media', $vars['object']->getPosseLinks()); ?>
            <?php if (empty($vars['object']->_id)) { 
                echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
            } ?>
            <?= $this->draw('content/extra'); ?>
            <?= $this->draw('content/access'); ?>
            <p class="button-bar ">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/media/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Publish" />

            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>