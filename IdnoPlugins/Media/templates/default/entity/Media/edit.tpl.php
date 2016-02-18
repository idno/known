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
                <?php

                    if (empty($vars['object']->_id)) {

                ?>
                <label>
                    <span class="btn btn-primary btn-file">
                        <i class="fa fa-play-circle"></i> <span id="media-filename">Upload audio</span> <input type="file" name="media" id="media" class="col-md-9" accept="audio/*;video/*;capture=microphone" onchange="$('#media-filename').html($(this).val())" />
                    </span>
                </label>
                <?php

                    }

                ?>
            </p>
            <div class="content-form">
                <label for="title">
                    Title</label>
                    <input type="text" name="title" id="title" placeholder="Give it a title" value="<?=htmlspecialchars($vars['object']->title)?>" class="form-control" />

            </idv>

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
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
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