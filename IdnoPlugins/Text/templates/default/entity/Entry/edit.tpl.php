<?= $this->draw('entity/edit/header'); ?>
<?php

    $autosave = new \Idno\Core\Autosave();
    if (!empty($vars['object']->body)) {
        $body = $vars['object']->body;
    } else {
        $body = $autosave->getValue('entry', 'bodyautosave');
    }
    if (!empty($vars['object']->title)) {
        $title = $vars['object']->title;
    } else {
        $title = $autosave->getValue('entry', 'title');
    }

    /* @var \Idno\Core\Template $this */

?>
    <form action="<?= $vars['object']->getURL() ?>" method="post">

        <div class="row">

            <div class="span8 offset2 edit-pane">


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
                <p>
                    <label>
                        Title<br/>
                        <input type="text" name="title" id="title" placeholder="Give it a title"
                               value="<?= htmlspecialchars($title) ?>" class="span8"/>
                    </label>
                </p>

                <p style="text-align: right">
                    <small>
                        <a href="#" onclick="tinymce.EditorManager.execCommand('mceRemoveEditor',true, 'body'); $('#plainTextSwitch').hide(); $('#richTextSwitch').show(); return false;" id="plainTextSwitch">Switch to plain text editor</a>
                        <a href="#" onclick="makeRichText('#body'); $('#plainTextSwitch').show(); $('#richTextSwitch').hide(); return false;" id="richTextSwitch" style="display:none">Switch to rich text editor</a></small></p>
                <p>
                    <label>
                        <textarea name="body"  placeholder="Tell your story"
                                  class="span8 bodyInput mentionable wysiwyg" id="body"><?= (htmlspecialchars($this->autop($body))) ?></textarea>
                    </label>
                </p>
                <?= $this->draw('entity/tags/input'); ?>

                <div class="wordcount" id="result">
                    Total words <strong><span id="totalWords">0</span></strong>
                </div>

                <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('article'); ?>

                <p class="button-bar ">
                    <?= \Idno\Core\site()->actions()->signForm('/entry/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>
                    <?= $this->draw('content/access'); ?>
                </p>

            </div>

        </div>
    </form>
    <div id="bodyautosave" style="display:none"></div>
    <script>

        /*function postForm() {
         var content = $('textarea[name="body"]').html($('#body').html());
         console.log(content);
         return content;
         }*/

        counter = function () {

            var value = $('#body').html(); // $('#body').val();
            if (value.length == 0) {
                $('#totalWords').html(0);
                $('#totalChars').html(0);
                $('#charCount').html(0);
                $('#charCountNoSpace').html(0);
                return;
            }

            var regex = /\S+/g;
            var wordCount = value.trim().replace(regex, ' ').split(' ').length;
            var totalChars = value.length;
            var charCount = value.trim().length;
            var charCountNoSpace = value.replace(regex, '').length;

            $('#totalWords').html(wordCount);
            $('#totalChars').html(totalChars);
            $('#charCount').html(charCount);
            $('#charCountNoSpace').html(charCountNoSpace);

        };

        $(document).ready(function () {
            $('#body').change(counter);
            $('#body').keydown(counter);
            $('#body').keypress(counter);
            $('#body').keyup(counter);
            $('#body').blur(counter);
            $('#body').focus(counter);
        });

        $(document).ready(function () {
            makeRichText('#body');
        });

        function makeRichText(container) {
            $(container).tinymce({
                selector: 'textarea',
                theme: 'modern',
                skin: 'light',
                menubar: false,
                toolbar: 'styleselect | bold italic | link image | blockquote | alignleft aligncenter alignright | code',
                plugins: 'code link image',
                file_picker_callback: function (callback, value, meta) {
                    filePickerDialog(callback, value, meta);
                },
                setup: function(ed) {
                    ed.on('keyup', function(e) {
                        //console.log('Editor contents was modified. Contents: ' + ed.getContent());
                        //check_submit();
                        counter();
                    });
                }
            });
        }

        function filePickerDialog(callback, value, meta) {
            tinymce.activeEditor.windowManager.open({
                title: 'File Manager',
                url: '<?=\Idno\Core\site()->config()->getDisplayURL()?>file/picker/?type=' + meta.filetype,
                width: 650,
                height: 550
            }, {
                oninsert: function (url) {
                    callback(url);
                }
            });
        }

        // Autosave the title & body
        autoSave('entry', ['title', 'body']);
    </script>
<?= $this->draw('entity/edit/footer'); ?>