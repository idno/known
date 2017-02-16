<?= $this->draw('entity/edit/header'); ?>
<form action="<?= $vars['object']->getURL() ?>" method="post">

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">
            <h4>
                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Bookmark<?php
                    } else {
                        ?>Edit Bookmark<?php
                    }

                ?>
            </h4>

            <div class="content-form">
                <label for="body">
                    Link Address</label>
                <input required type="url" name="body" id="body" placeholder="http://...."
                       value="<?php if (empty($vars['url'])) {
                           echo htmlspecialchars($vars['object']->body);
                       } else {
                           echo htmlspecialchars($vars['url']);
                       } ?>" class="form-control bookmark-url"/>
                </label>
                <?php

                    if (empty($vars['url'])) {

                        ?>

                        <div class="bookmark-spinner-container">
                            <div class="spinner bookmark-title-spinner" style="display:none">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>

                        <?php

                    }

                ?>
                <div class="bookmark-title-container" for="title"
                     <?php if (empty($vars['object']->pageTitle) && empty($vars['object']->_id) && (empty($vars['url']) && empty($vars['object']->body))) { ?>style="display:none"<?php } ?>>
                    <label for="title">
                        Title<br/>
                    </label>
                    <input required type="text" name="title" id="title" placeholder="Page name"
                           value="<?php
                               echo htmlspecialchars($vars['object']->pageTitle);
                           ?>" class="form-control bookmark-title"/>
                </div>

                <?= $this->__([
                    'name'        => 'description',
                    'value'       => $vars['object']->description,
                    'wordcount'   => false,
                    'class'       => 'wysiwyg-short',
                    'height'      => 250,
                    'placeholder' => 'Add notes to your bookmark...',
                    'label'       => 'Description'
                ])->draw('forms/input/richtext') ?>
            </div>
            <?= $this->draw('entity/tags/input'); ?>
            <?php echo $this->drawSyndication('bookmark', $vars['object']->getPosseLinks()); ?>
            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to"
                                                              value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?= $this->draw('content/access'); ?>
            <p class="button-bar">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/like/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <input type="submit" class="btn btn-primary" value="Save"/>

            </p>
        </div>

    </div>
</form>
<?= $this->draw('entity/edit/footer'); ?>
<script language="javascript">

    $(document).ready(function () {

        $('.bookmark-url').change(function () {

            if ($('bookmark-url').val() != "") {
                $('.bookmark-title-spinner').show();
                $.ajax({
                    dataType: "json",
                    url: "<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>like/callback/",
                    data: {
                        url: $('.bookmark-url').val()
                    },
                    success: function (data) {
                        $('.bookmark-title').val(htmlEntityDecode(data.value));
                        $('.bookmark-spinner-container').html(" ");
                        $('.bookmark-title-container').show();
                    },
                    error: function () {
                        $('.bookmark-spinner-container').html(" ");
                        $('.bookmark-title-container').show();
                    }
                });
            }

        });

    })

</script>