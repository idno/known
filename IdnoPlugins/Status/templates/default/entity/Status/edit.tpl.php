<?php

    if (!empty($vars['object']->inreplyto)) {
        if (!is_array($vars['object']->inreplyto)) {
            $vars['object']->inreplyto = [$vars['object']->inreplyto];
        }
    } else {
        $vars['object']->inreplyto = [];
    }
    if (!empty($vars['url'])) {
        $vars['object']->inreplyto = [$url];
    }

?>
<form action="<?= $vars['object']->getURL() ?>" method="post">

    <div class="row">

        <div class="span10 offset1">

            <p>
                <small><a href="#"
                          onclick="$('#inreplyto').append('<span><input type=&quot;url&quot; name=&quot;inreplyto[]&quot; value=&quot;&quot; placeholder=&quot;The website address of the post you\'re replying to&quot; class=&quot;span8&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;>Remove</a></small><br /></span>'); return false;">+
                        Add a site you're replying to</a></small>
            </p>
            <div id="inreplyto">
                <?php
                    if (!empty($vars['object']->inreplyto)) {
                        foreach ($vars['object']->inreplyto as $inreplyto) {
                            ?>
                            <p>
                                <input type="text" name="inreplyto[]"
                                       placeholder="The website address of the post you're replying to"
                                       class="span8" value="<?= htmlspecialchars($inreplyto) ?>"/>
                                <small><a href="#"
                                          onclick="$(this).parent().parent().remove(); return false;">Remove</a></small>
                            </p>
                        <?php
                        }
                    }
                ?>
            </div>

            <p>
                <label>
                    <?php
                        if (empty($vars['url']) && empty($vars['object']->inreplyto)) {
                            echo 'What\'s going on?';
                        } else {
                            echo 'Your message:';
                        }
                    ?>
                </label>
            </p>
            <textarea name="body" id="body" class="span8"><?php if (!empty($vars['body'])) {
                    echo htmlspecialchars($vars['body']);
                } else {
                    echo htmlspecialchars($vars['object']->body);
                } ?></textarea>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('note'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save"/>
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= $this->draw('content/access'); ?>
            </p>
        </div>

    </div>
</form>