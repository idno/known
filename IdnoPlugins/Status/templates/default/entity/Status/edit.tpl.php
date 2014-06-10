<?php

    if (!empty($vars['object']->inreplyto)) {
        if (!is_array($vars['object']->inreplyto)) {
            $vars['object']->inreplyto = [$vars['object']->inreplyto];
        }
    } else {
        $vars['object']->inreplyto = [];
    }
    if (!empty($vars['url'])) {
        $vars['object']->inreplyto = [$vars['url']];
    }

?>
<form action="<?= $vars['object']->getURL() ?>" method="post">

    <div class="row">

        <div class="span8 offset1">

            <p>
                <small><a id="inreplyto-add" href="#"
                          onclick="$('#inreplyto').append('<span><input required type=&quot;url&quot; name=&quot;inreplyto[]&quot; value=&quot;&quot; placeholder=&quot;The website address of the post you\'re replying to&quot; class=&quot;span8&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;>Remove</a></small><br /></span>'); return false;">+
                        Add a site you're replying to</a></small>
            </p>
            <div id="inreplyto">
                <?php
                    if (!empty($vars['object']->inreplyto)) {
                        foreach ($vars['object']->inreplyto as $inreplyto) {
                            ?>
                            <p>
                                <input type="url" name="inreplyto[]"
                                       placeholder="The website address of the post you're replying to"
                                       class="span8" value="<?= htmlspecialchars($inreplyto) ?>"/>
                                <small><a href="#"
                                          onclick="$(this).parent().parent().remove(); return false;">Remove</a></small>
                            </p>
                        <?php
                        }
                    }
                ?>
                <?php
                    $twitter_user = null;
                    $u = \Idno\Core\site()->currentPage()->getInput('replyto');
                    if (preg_match('/https?:\/\/(www\.)?twitter\.com\/([^\/]+)/', $u, $matches)) {
                        $twitter_user = $matches[2];
                    }
                    
                    if (!empty($u)) {
                        ?>
                            <span><input required type="url" name="inreplyto[]" value="<?= $u; ?>" placeholder="The website address of the post you\'re replying to" class="span8" /> <small><a href="#" onclick="$(this).parent().parent().remove(); return false;">Remove</a></small><br /></span> 
                        <?php
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

            <textarea required name="body" id="body" style="width: 100%"><?php 
            
                if (!empty($twitter_user))
                    echo htmlspecialchars ("@$twitter_user ");
            
                if (!empty($vars['body'])) {
                    echo htmlspecialchars($vars['body']);
                } else {
                    echo htmlspecialchars($vars['object']->body);
                } ?></textarea>

        </div>
        <div class="span8 offset1">

            <p id="counter" style="display:none" class="pull-right">
                <span class="count"></span>
            </p>
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('note'); ?>
            <p>
                <?= \Idno\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save"/>
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= $this->draw('content/access'); ?>
            </p>
            <p>
                <small><a href="#" onclick="$('#bookmarklet').toggle(); return false;">Get a button for your browser</a></small>
            </p>

            <div id="bookmarklet" style="display:none;">
                <p>Drag the following link into your browser links bar to easily share links or reply to posts on other sites:</p>
                <?= $this->draw('entity/bookmarklet'); ?>
            </div>     
        </div>
        <div class="span2">
            <p id="counter" style="display:none">
                <span class="count"></span>
            </p>
        </div>

               
    </div>
</form>
<script>
    $(document).ready(function () {
        $('#body').keyup(function () {
            var len = $(this).val().length;

            if (len > 0) {
                if (!$('#counter').is(":visible")) {
                    $('#counter').fadeIn();
                }
            }

            $('#counter .count').text(len);


        });
        
        // Make in reply to a little less painful
        $("#inreplyto-add").on('dragenter', function(e) {
            e.stopPropagation();
            e.preventDefault();
            $('#inreplyto').append('<span><input required type="url" name="inreplyto[]" value="" placeholder="The website address of the post you\'re replying to" class="span8" /> <small><a href="#" onclick="$(this).parent().parent().remove(); return false;">Remove</a></small><br /></span>');
        });
    });
</script>