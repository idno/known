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
                        Page address</label>
                        <input required type="url" name="body" id="body" placeholder="http://...."
                               value="<?php if (empty($vars['url'])) {
                                   echo htmlspecialchars($vars['object']->body);
                               } else {
                                   echo htmlspecialchars($vars['url']);
                               } ?>" class="form-control"/>

                     <!--<label for="title">
                        Page title</label>
                        <input required type="text" name="title" id="title" placeholder="Page name"
                               value="<?php
                                   echo htmlspecialchars($vars['object']->title);
                               ?>" class="form-control"/>-->

                    <label for="description">
                        Comments

                    </label>

                    <textarea name="description" id="description" class="form-control"
                              placeholder="This page is great because... Use hashtags to organize your bookmark."><?= htmlspecialchars($vars['object']->description); ?></textarea>
                </div>
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