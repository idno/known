<?= $this->draw('entity/edit/header'); ?>
<?php

    /* @var \Idno\Core\Template $this */

    if (!empty($vars['object'])) {
        $title       = $vars['object']->getTitle();
        $body        = $vars['object']->body;
        $forward_url = $vars['object']->forward_url;
        $hide_title  = $vars['object']->hide_title;
    }

    if ($title == 'Untitled') {
        $title = '';
    }

?>
    <form action="<?= $vars['object']->getURL() ?>" method="post">

        <div class="row">

            <div class="col-md-8 col-md-offset-2 edit-pane">


                <?php

                    if (empty($vars['object']->_id)) {

                        ?>
                        <h4>New Page</h4>
                    <?php

                    } else {

                        ?>
                        <h4>Edit Page</h4>
                    <?php

                    }

                ?>
                <p>
                    <label for="title">
                        Title</label>
                    <input type="text" name="title" id="title" placeholder="Give it a title"
                           value="<?= htmlspecialchars($title) ?>" class="form-control"/>
                </p>



                <?= $this->__([
                    'name' => 'body',
                    'value' => $vars['object']->body,
                    'wordcount' => false,
                    'class' => 'wysiwyg',
                    'height' => 100,
                    'placeholder' => 'Tell your story',
                    'label' => 'Body'
                ])->draw('forms/input/richtext')?>

                <?= $this->draw('entity/tags/input'); ?>

                <div class="page-cat">
                    <label>
                        Parent category</label><br>
                    <select name="category" class="selectpicker">
                        <option <?php if ($vars['category'] == 'No Category') {
                            echo 'selected';
                        } ?>>No Category
                        </option>
                        <?php

                            if (!empty($vars['categories'])) {
                                foreach ($vars['categories'] as $category) {

                                    ?>
                                    <option <?php if ($category == $vars['category']) {
                                        echo 'selected';
                                    } ?>><?= htmlspecialchars($category) ?></option>
                                <?php

                                }
                            }

                        ?>
                    </select>

                </div>

                <p id="show-options">
                    <small><a href="#" onclick="$('#moreoptions').toggle(); $('#show-options').hide(); return false;"><i
                                class="fa fa-plus"></i>
                            Show advanced options</a></small>
                </p>
                <div id="moreoptions" <?php
                    if (empty($hide_title) && empty($forward_url)) {
                        ?>
                        style="display:none"
                    <?php
                    }
                ?>>

                    <p id="hide-options">
                        <small><a href="#"
                                  onclick="$('#moreoptions').toggle(); $('#show-options').show(); return false;"><i
                                    class="fa fa-minus"></i>
                                Hide advanced options</a></small>
                    </p>

                    <div>
                        <p>
                            <label for="forward_url">
                                Forward URL</label>
                            <input type="text" name="forward_url" id="forward_url"
                                   placeholder="Website to forward users to"
                                   value="<?= htmlspecialchars($forward_url) ?>" class="form-control"/>
                            <small>Most of the time, you should leave this blank. Include a URL here if you want users
                                to
                                be forwarded to an external page instead of displaying page content.
                            </small>
                        </p>
                    </div>
                    <p style="margin-bottom: 20px">
                        <strong>Show the page title as a heading?</strong><br>
                        <label class="radio-inline">
                            <input type="radio" name="hide_title" id="title-heading" value="0" <?php

                                if (empty($hide_title)) {
                                    echo 'checked';
                                }

                            ?>>
                            Yes
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="hide_title" id="title-heading" value="1" <?php

                                if (!empty($hide_title)) {
                                    echo 'checked';
                                }

                            ?>>
                            No
                        </label>
                    </p>

                    <div class="page-cat">


                    </div>

                </div>

                <?= $this->draw('content/access'); ?>

                <p class="button-bar " style="text-align: right">
                    <?= \Idno\Core\Idno::site()->actions()->signForm('/staticpages/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Publish"/>
                </p>

            </div>

        </div>
    </form>
    <script>

        /*function postForm() {
         var content = $('textarea[name="body"]').html($('#body').html());
         console.log(content);
         return content;
         }*/

        $(document).ready(function () {
            makeRich('#body');
        })
        ;

        function makeRich(container) {
            $(container).tinymce({
                selector: 'textarea',
                theme: 'modern',
                skin: 'light',
                statusbar: false,
                menubar: false,
                toolbar: 'styleselect | bold italic | link image | blockquote bullist numlist | alignleft aligncenter alignright | code',
                plugins: 'code link image autoresize',
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                file_picker_callback: function (callback, value, meta) {
                    filePickerDialog(callback, value, meta);
                }
            });
        }

        function filePickerDialog(callback, value, meta) {
            tinymce.activeEditor.windowManager.open({
                title: 'File Manager',
                url: '<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>filepicker/?type=' + meta.filetype,
                width: 650,
                height: 550
            }, {
                oninsert: function (url) {
                    callback(url);
                }
            });
        }

        //$('.selectpicker').selectpicker();

    </script>
<?= $this->draw('entity/edit/footer'); ?>