<?php echo $this->draw('entity/edit/header'); ?>
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
if (!empty($vars['object']->short_description)) {
    $subtitle = $vars['object']->short_description;
} else {
    $subtitle = '';
}
if (!empty($vars['object'])) {
    $object = $vars['object'];
} else {
    $object = false;
}
    $unique_id = 'body' . rand(0, 9999);

    /* @var \Idno\Core\Template $this */
?>
<?php
if (!empty($vars['object']->inreplyto)) {
    if (!is_array($vars['object']->inreplyto)) {
        $vars['object']->inreplyto = array($vars['object']->inreplyto);
    }
} else {
    $vars['object']->inreplyto = array();
}
if (!empty($vars['url'])) {
    $vars['object']->inreplyto = array($vars['url']);
}
?>
    <form action="<?= $vars['object']->getURL() ?>" method="post">

        <div class="idno-editor">

                <?php if (empty($vars['object']->_id)) { ?>
                    <h4 class="idno-editor-heading"><?= \Idno\Core\Idno::site()->language()->_('New Post') ?></h4>
                <?php } else { ?>
                    <h4 class="idno-editor-heading"><?= \Idno\Core\Idno::site()->language()->_('Edit Post') ?></h4>
                <?php } ?>

                <div class="idno-form-field">
                    <label class="idno-label" for="title"><?= \Idno\Core\Idno::site()->language()->_('Title') ?></label>
                    <?= $this->__(['name' => 'title', 'placeholder' => \Idno\Core\Idno::site()->language()->_('Give it a title'), 'id' => 'title', 'value' => $title, 'required' => true, 'class' => 'idno-input idno-editor-title'])->draw('forms/input/input') ?>
                </div>

                <div class="idno-form-field">
                    <label class="idno-label" for="subtitle"><?= \Idno\Core\Idno::site()->language()->_('Subtitle') ?></label>
                    <?= $this->__(['name' => 'subtitle', 'placeholder' => \Idno\Core\Idno::site()->language()->_('Optional sub title for this post'), 'id' => 'subtitle', 'value' => $subtitle, 'class' => 'idno-input idno-editor-subtitle'])->draw('forms/input/input') ?>
                </div>

                <?= $this->__([
                    'name' => 'body',
                    'unique_id' => $unique_id,
                    'value' => $body,
                    'object' => $object,
                    'wordcount' => true,
                    'required' => true
                ])->draw('forms/input/richtext') ?>

                <?= $this->draw('entity/tags/input') ?>
                <?= $this->draw('content/unfurl') ?>

                <?php
                // Set focus so you can start typing straight away (on shares)
                if (\Idno\Core\Idno::site()->currentPage()->getInput('share_url')) {
                    ?>
                    <script>
                        $(document).ready(function(){
                            var content = $('#title').val();
                            var len = content.length;
                            $('#title').focus(function(){
                                $(this).prop('selectionStart', len);
                            });
                            $('#title').focus();
                        });
                    </script>
                    <?php
                }
                ?>

                <div class="idno-form-field">
                    <a id="inreplyto-add" href="#" class="idno-link-subtle"
                       onclick="$('#inreplyto').append('<div class=&quot;idno-reply-field&quot;><input required type=&quot;url&quot; name=&quot;inreplyto[]&quot; value=&quot;&quot; placeholder=&quot;<?= addslashes(\Idno\Core\Idno::site()->language()->_('Add the URL that you\'re replying to')) ?>&quot; class=&quot;idno-input&quot; onchange=&quot;adjust_content(this.value)&quot; /> <a href=&quot;#&quot; class=&quot;idno-link-danger&quot; onclick=&quot;$(this).parent().remove(); return false;&quot;><?= \Idno\Core\Idno::site()->language()->esc_('Remove') ?></a></div>'); return false;">
                        <?= \Idno\Core\Idno::site()->language()->_('Reply to a site') ?>
                    </a>
                </div>

                <div id="inreplyto">
                    <?php
                    if (!empty($vars['object']->inreplyto)) {
                        foreach ($vars['object']->inreplyto as $inreplyto) {
                            ?>
                            <div class="idno-reply-field">
                                <input type="url" name="inreplyto[]"
                                       placeholder="<?= \Idno\Core\Idno::site()->language()->_('Add the URL that you\'re replying to') ?>"
                                       class="idno-input" value="<?= htmlspecialchars($inreplyto) ?>" onchange="adjust_content(this.value)"/>
                                <a href="#" class="idno-link-danger"
                                   onclick="$(this).parent().remove(); return false;">
                                    <?= \Idno\Core\Idno::site()->language()->_('Remove') ?>
                                </a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <?= $this->drawSyndication('article', $vars['object']->getPosseLinks()) ?>
                <?php if (empty($vars['object']->_id)) {
                    echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
                } ?>

                <?= $this->draw('content/extra') ?>
                <?= $this->draw('content/access') ?>

                <?= \Idno\Core\Idno::site()->actions()->signForm('/entry/edit') ?>

                <div style="display:flex;gap:0.5rem;margin-top:var(--spacing-section)">
                    <button type="submit" class="idno-btn idno-btn-primary" name="publish_status" value="published">
                        <?= \Idno\Core\Idno::site()->language()->_('Publish') ?>
                    </button>
                    <button type="submit" class="idno-btn idno-btn-ghost" name="publish_status" value="draft">
                        <?= \Idno\Core\Idno::site()->language()->_('Save as Draft') ?>
                    </button>
                </div>

        </div>
    </form>
<?php echo $this->draw('entity/edit/footer'); ?>
<script>

    function adjust_content(url) {
        var username = url.match(/https?:\/\/([a-z]+\.)?twitter\.com\/(#!\/)?@?([^\/]*)/)[3];
        if (username != null) {
            if ($('#title').val().search('@' + username) == -1) {
                $('#title').val('@' + username + ' ' + $('#title').val());
            }
        }
    }

    $(document).ready(function () {

        // Make in reply to a little less painful
        $("#inreplyto-add").on('dragenter', function(e) {
            var placeholder = '<?= addslashes(\Idno\Core\Idno::site()->language()->esc_('Add the URL that you\'re replying to')) ?>';
            e.stopPropagation();
            e.preventDefault();
            $('#inreplyto').append('<div class="idno-reply-field"><input required type="url" name="inreplyto[]" value="" placeholder="' + placeholder + '" class="idno-input" onchange="adjust_content(this.value)" /> <a href="#" class="idno-link-danger" onclick="$(this).parent().remove(); return false;"><?= \Idno\Core\Idno::site()->language()->esc_('Remove') ?></a></div>'); return false;
        });
    });

    $(document).ready(function(){
        // Autosave the title & body
        autoSave('entry', ['title', 'body'], {
          'body': '#<?= $unique_id ?>',
        });
    });

</script>
