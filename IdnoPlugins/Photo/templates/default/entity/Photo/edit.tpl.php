<?=$this->draw('entity/edit/header');?>
<form action="<?=$vars['object']->getURL()?>" method="post" enctype="multipart/form-data">

    <div class="row">

        <div class="span8 offset2">
        
         	<h4> 
                                 <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Photo<?php
                    } else {
                        ?>Edit Photo<?php
                    }

                ?>
             </h4>

            <p>
                <?php

                    if (empty($vars['object']->_id)) {

                ?>
                <label>
                    <span class="btn btn-primary btn-file">
                        <i class="icon-camera"></i> <span id="photo-filename">Select a photo</span> <input type="file" name="photo" id="photo" class="span9" accept="image/*;capture=camera" onchange="$('#photo-filename').html($(this).val())" />
                    </span>
                </label>
                <?php

                    }

                ?>
            </p>
            <p>
                <label>
                    Title<br />
                    <input type="text" name="title" id="title" value="<?=htmlspecialchars($vars['object']->title)?>" class="span8" placeholder="Give it a title"/>
                </label>
            </p>
            <p>
                <label>
                    Description<br />
                    <textarea name="description" id="description" class="span8 bodyInput mentionable" placeholder="Add a caption or include some #tags"><?=htmlspecialchars($vars['object']->description)?></textarea>
                </label>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('image'); ?>
            <p class="button-bar ">
                <?= \Idno\Core\site()->actions()->signForm('/photo/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" />
                <input type="submit" class="btn btn-primary" value="Publish" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<?=$this->draw('entity/edit/footer');?>