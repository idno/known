<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span8 offset2 edit-pane">
        
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
                        <i class="icon-play-circle"></i> <span id="media-filename">Upload media</span> <input type="file" name="media" id="media" class="span9" accept="audio/*;video/*;capture=audio" onchange="$('#media-filename').html($(this).val())" />
                    </span>
                </label>
                <?php

                    }

                ?>
            </p>
            <p>
                <label>
                    Title<br />
                    <input type="text" name="title" id="title" placeholder="Give it a title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span8" />
                </label>
            </p>
            <p>
                <label>
                    Description<br />
                    <textarea name="body" id="description" placeholder="What's this about?" class="span8 bodyInput"><?=htmlspecialchars($vars['object']->body)?></textarea>
                </label>
            </p>
                <label>
                     Tags<br/>
                     <input type="text" name="tags" id="tags" placeholder="Add some #tags"
                               value="<?= htmlspecialchars($vars['object']->tags) ?>" class="span8"/>
                </label>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('media'); ?>
            <p class="button-bar ">
                <?= \Idno\Core\site()->actions()->signForm('/media/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Publish" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>