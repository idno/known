<?php echo $this->draw('entity/edit/header'); ?>
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

            <h4 class="idno-editor-heading">
                <?php
                if (empty($vars['object']->_id)) {
                    echo \Idno\Core\Idno::site()->language()->_('New Status Update');
                } else {
                    echo \Idno\Core\Idno::site()->language()->_('Edit Status Update');
                }
                ?>
            </h4>

            <p id="counter" style="display:none" class="idno-char-counter">
                <span class="count"></span>
            </p>

            <?php
                $body = "";
            if (!empty($vars['body'])) {
                $body = $vars['body'];
            } else {
                $body = $vars['object']->body;
            } ?>
            <?= $this->__([
                'unique_id' => 'body',
                'name' => 'body',
                'placeholder' => \Idno\Core\Idno::site()->language()->_("Share a quick note or comment. You can use links and #hashtags."),
                'required' => true,
                'class' => 'idno-textarea ctrl-enter-submit',
                'value' => $body,
                'height' => 140
            ])->draw('forms/input/longtext') ?>
            <?php

                echo $this->draw('entity/tags/input');

            // Set focus so you can start typing straight away (on shares)
            if (\Idno\Core\Idno::site()->currentPage()->getInput('share_url')) {
                ?>
            <script>
                $(document).ready(function(){
                    var content = $('#body').val();
                    var len = content.length;
                    $('#body').focus(function(){
                        $(this).prop('selectionStart', len);
                    });
                    $('#body').focus();
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

            <?php if (empty($vars['object']->_id)) {
                echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
            } ?>
            <?= $this->drawSyndication('note', $vars['object']->getPosseLinks()) ?>
            <?= $this->draw('content/extra') ?>
            <?= $this->draw('content/access') ?>

            <?= \Idno\Core\Idno::site()->actions()->signForm('/status/edit') ?>

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
<script src="<?= \Idno\Core\Idno::site()->config()->getStaticURL() ?>IdnoPlugins/Status/external/brevity-js/brevity.js"></script>
<script>
    function adjust_content(url) {
        var username = url.match(/https?:\/\/([a-z]+\.)?twitter\.com\/(#!\/)?@?([^\/]*)/)[3];
        if (username != null) {
            if ($('#body').val().search('@' + username) == -1) {
                $('#body').val('@' + username + ' ' + $('#body').val());
                count_chars();
            }
        }
    }

    function count_chars() {
        var len = brevity.tweetLength($('#body').val());

        if (len > 0) {
            if (!$('#counter').is(":visible")) {
                $('#counter').fadeIn();
            }
        }

        $('#counter .count').text(len);
    }

    $(document).ready(function () {
        $('#body').keyup(function () {
            count_chars();
        });

        // Make in reply to a little less painful
        $("#inreplyto-add").on('dragenter', function(e) {
            var placeholder = '<?= addslashes(\Idno\Core\Idno::site()->language()->_('Add the URL that you\'re replying to')) ?>';
            e.stopPropagation();
            e.preventDefault();
            $('#inreplyto').append('<div class="idno-reply-field"><input required type="url" name="inreplyto[]" value="" placeholder="' + placeholder + '" class="idno-input" onchange="adjust_content(this.value)" /> <a href="#" class="idno-link-danger" onclick="$(this).parent().remove(); return false;"><?= \Idno\Core\Idno::site()->language()->esc_('Remove') ?></a></div>'); return false;
        });
    });
</script>
<?php echo $this->draw('entity/edit/footer');
