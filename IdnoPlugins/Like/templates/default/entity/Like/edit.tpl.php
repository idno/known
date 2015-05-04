<?= $this->draw('entity/edit/header'); ?>
    <form action="<?= $vars['object']->getURL() ?>" method="post">

        <div class="row">

            <div class="span8 offset2 edit-pane">
                <h4>
                    <?php

                        if (empty($vars['object']->_id)) {
                            ?>New Bookmark<?php
                        } else {
                            ?>Edit Bookmark<?php
                        }

                    ?>
                </h4>

                <p>
                    <label>
                        Page address<br/>
                        <input required type="url" name="body" id="body" placeholder="http://...."
                               value="<?php if (empty($vars['url'])) {
                                   echo htmlspecialchars($vars['object']->body);
                               } else {
                                   echo htmlspecialchars($vars['url']);
                               } ?>" class="span8 bookmark-url"/>
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
                    <label class="bookmark-title-container" <?php if (empty($vars['object']->pageTitle) && empty($vars['object']->_id)) { ?>style="display:none"<?php } ?>>
                        Page title<br/>
                        <input required type="text" name="title" id="title" placeholder="Page name"
                               value="<?php
                                   echo htmlspecialchars($vars['object']->pageTitle);
                               ?>" class="span8 bookmark-title"/>
                    </label>
                    <label>
                        Comments<br/>

                    </label>

                    <textarea name="description" id="description" class="span8"
                              placeholder="This page is great because... Use hashtags to organize your bookmark."><?= htmlspecialchars($vars['object']->description); ?></textarea>
                </p>
                <?=$this->draw('entity/tags/input');?>
                <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('bookmark'); ?>
                <p class="button-bar">
                    <?= \Idno\Core\site()->actions()->signForm('/like/edit') ?>
                    <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="Save"/>
                    <?= $this->draw('content/access'); ?>
                </p>
            </div>

        </div>
    </form>
<?= $this->draw('entity/edit/footer'); ?>
<script language="javascript">

    $(document).ready(function() {

        $('.bookmark-url').change(function() {

            if ($('bookmark-url').val() != "") {
                $('.bookmark-title-spinner').show();
                $.ajax({
                    dataType: "json",
                    url: "<?=\Idno\Core\site()->config()->getDisplayURL()?>like/callback/",
                    data: {
                        url: $('.bookmark-url').val()
                    },
                    success: function(data) {
                        $('.bookmark-title').val(data.value);
                        $('.bookmark-spinner-container').html(" ");
                        $('.bookmark-title-container').show();
                    },
                    error: function() {
                        $('.bookmark-spinner-container').html(" ");
                        $('.bookmark-title-container').show();
                    }
                });
            }

        });

    })

</script>