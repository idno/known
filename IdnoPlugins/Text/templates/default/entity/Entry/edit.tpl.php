<?=$this->draw('entity/edit/header');?>
<?php

    $autosave = new \Idno\Core\Autosave();
    if (!empty($vars['object']->body)) {
        $body = $vars['object']->body;
    } else {
        $body = $autosave->getValue('entry','body');
    }
    if (!empty($vars['object']->title)) {
        $title = $vars['object']->title;
    } else {
        $title = $autosave->getValue('entry','title');
    }

?>
<form action="<?=$vars['object']->getURL()?>" method="post">

    <div class="row">

        <div class="span10 offset2">
            <p id="counter" style="display:none" class="pull-right">
                <span class="count"></span>
            </p>
        <h5>New Post</h5>

            <p>
                <label>
                    Title<br />
                    <input type="text" name="title" id="title" placeholder="Give it a title" value="<?=htmlspecialchars($title)?>" class="span8" />
                </label>
            </p>
            <p>
                <label>
                    Body<br />
                    <textarea required name="body" id="body" placeholder="Tell your story" class="span8 bodyInput mentionable"><?=htmlspecialchars($body)?></textarea>
                </label>
            </p>

            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('article'); ?>
            <p class="note">Posts support <strong>text</strong> and <strong>markup</strong>. Feel free to add <strong>#tags</strong>.</p>
            <p class="button-bar span8">
                <?= \Idno\Core\site()->actions()->signForm('/text/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();" /> 
                <input type="submit" class="btn btn-primary" value="Publish" />
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>
<script>
   /*$(document).ready(function () {
        $('#body').keyup(function () {
            var len = $(this).val().length;

            if (len > 0) {
                if (!$('#counter').is(":visible")) {
                    $('#counter').fadeIn();
                }
            }

            $('#counter .count').text(len);


        });*/
        
    // Autosave the title & body
    autoSave('entry', ['title','body']);
</script>
<?=$this->draw('entity/edit/footer');?>