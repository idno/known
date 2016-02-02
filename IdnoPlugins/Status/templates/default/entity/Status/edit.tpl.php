<?=$this->draw('entity/edit/header');?>
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

    <div class="row">

        <div class="col-md-8 col-md-offset-2 edit-pane">

            <p id="counter" style="display:none" class="pull-right">
                <span class="count"></span>
            </p>

            <h4>
                <?php

                    if (empty($vars['object']->_id)) {
                        ?>New Status Update<?php
                    } else {
                        ?>Edit Status Update<?php
                    }

                ?>
            </h4>

            <textarea required name="body" id="body" class="content-entry mentionable form-control" placeholder="Share a quick note or comment. You can use links and #hashtags."><?php

                if (!empty($vars['body'])) {
                    echo htmlspecialchars($vars['body']);
                } else {
                    echo htmlspecialchars($vars['object']->body);
                } ?></textarea>
            <?php

                echo $this->draw('entity/tags/input');

            // Set focus so you can start typing straight away (on shares)
            if (\Idno\Core\Idno::site()->currentPage()->getInput('share_url')) {
            ?>
            <script>
                $(document).ready(function(){
                    var content = $('#body').val();
                    var len = content.length;
                    var element = $('#body');

                    $('#body').focus(function(){
                        $(this).prop('selectionStart', len);
                    });
                    $('#body').focus();
                });
            </script>
            <?php
            }
            ?>

            <p>
                <small><a id="inreplyto-add" href="#"
                          onclick="$('#inreplyto').append('<span><input required type=&quot;url&quot; name=&quot;inreplyto[]&quot; value=&quot;&quot; placeholder=&quot;Add the URL that you\'re replying to&quot; class=&quot;form-control&quot; onchange=&quot;adjust_content(this.value)&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;><icon class=&quot;fa fa-times&quot;></icon> Remove URL</a></small><br /></span>'); return false;"><i class="fa fa-reply"></i>
                        Reply to a site</a></small>
            </p>


            <div id="inreplyto">
                <?php
                    if (!empty($vars['object']->inreplyto)) {
                        foreach ($vars['object']->inreplyto as $inreplyto) {
                            ?>
                            <p>
                                <input type="url" name="inreplyto[]"
                                       placeholder="Add the URL that you're replying to"
                                       class="form-control inreplyto" value="<?= htmlspecialchars($inreplyto) ?>" onchange="adjust_content(this.value)"/>
                                <small><a href="#"
                                          onclick="$(this).parent().parent().remove(); return false;"><i class="fa fa-times"></i>
                                          Remove URL</a></small>
                            </p>
                        <?php
                        }
                    }
                ?>
            </div>

            <?php if (empty($vars['object']->_id)) { ?><input type="hidden" name="forward-to" value="<?= \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'; ?>" /><?php } ?>
            <?php echo $this->drawSyndication('note', $vars['object']->getPosseLinks()); ?>
            <?= $this->draw('content/access'); ?>

            <p class="button-bar">
                <?= \Idno\Core\Idno::site()->actions()->signForm('/status/edit') ?>
                <input type="button" class="btn btn-cancel" value="Cancel" onclick="hideContentCreateForm();"/>
                <input type="submit" class="btn btn-primary" value="Publish"/>
            </p>
        </div>
        <div class="col-md-2">
            <p id="counter" style="display:none">
                <span class="count"></span>
            </p>
        </div>


    </div>
</form>
<script src="<?=\Idno\Core\Idno::site()->config()->getStaticURL()?>IdnoPlugins/Status/external/brevity-js/brevity.js"></script>
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
            e.stopPropagation();
            e.preventDefault();
            $('#inreplyto').append('<span><input required type="url" name="inreplyto[]" value="" placeholder="Add the URL that you\'re replying to" class="form-control" onchange="adjust_content(this.value)" /> <small><a href="#" onclick="$(this).parent().parent().remove(); return false;"><icon class="fa fa-times"></icon> Remove URL</a></small><br /></span>'); return false;
        });
    });
</script>
<?=$this->draw('entity/edit/footer');?>