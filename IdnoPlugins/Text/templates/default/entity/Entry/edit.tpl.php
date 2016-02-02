<?= $this->draw('entity/edit/header'); ?>
<?php
    $autosave = new \Idno\Core\Autosave();
    if (!empty($vars['object']->body)) {
        $body = $vars['object']->body;
    } else {
        $body = '';
    }
    if (!empty($vars['object']->title)) {
        $title = $vars['object']->title;
    } else {
        $title = '';
    }
    if (!empty($vars['object'])) {
        $object = $vars['object'];
    } else {
        $object = false;
    }
    $unique_id = 'body'.rand(0, 9999);

    /* @var \Idno\Core\Template $this */

?>
    <form action="<?= $vars['object']->getURL() ?>" method="post">

        <div class="row">

            <div class="col-md-8 col-md-offset-2 edit-pane">


                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <h4>New Post</h4>
                    <?php

                    } else {

                        ?>
                        <h4>Edit Post</h4>
                    <?php

                    }

                ?>

                <div class="content-form">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" placeholder="Give it a title" value="<?= htmlspecialchars($title) ?>" class="form-control"/>
                </div>

                <?= $this->__([
                    'name' => 'body',
                    'unique_id' => $unique_id,
                    'value' => $body,
                    'object' => $object,
                    'wordcount' => true
                ])->draw('forms/input/richtext')?>
                <?= $this->draw('entity/tags/input'); ?>

                <?php echo $this->drawSyndication('article', $vars['object']->getPosseLinks()); ?>
                <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>

                <?= $this->draw('content/access'); ?>

                <p class="button-bar ">

                    <?= \Idno\Core\Idno::site()->actions()->signForm('/entry/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="tinymce.EditorManager.execCommand('mceRemoveEditor',true, 'body'); hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>

                </p>

            </div>

        </div>
    </form>
<?= $this->draw('entity/edit/footer'); ?>
<script>

    // Autosave the title & body
    autoSave('entry', ['title', 'body'], {
      'body': '#<?=$unique_id?>',
    });

</script>
