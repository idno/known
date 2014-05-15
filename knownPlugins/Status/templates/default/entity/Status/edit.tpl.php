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
            
            <textarea required name="body" id="body" class="span8 pull-left"><?php if (!empty($vars['body'])) {
                    echo htmlspecialchars($vars['body']);
                } else {
                    echo htmlspecialchars($vars['object']->body);
                } ?></textarea>
            <p id="counter" class="span2 pull-right progress" style="display:none;">
                <span class="bar" style="width: 0%;"> </span> 
                </p>
            
            <?php if (empty($vars['object']->_id)) echo $this->drawSyndication('note'); ?>
            <p>
                <?= \known\Core\site()->actions()->signForm('/status/edit') ?>
                <input type="submit" class="btn btn-primary" value="Save"/>
                <input type="button" class="btn" value="Cancel" onclick="hideContentCreateForm();"/>
                <?= $this->draw('content/access'); ?>
            </p>
            
            
        </div>
        

    </div>
</form>
<script>
    $(document).ready(function(){
       $('#body').keyup(function() {
            var len = $(this).val().length;
           
           // Show / hide
           if (len<140/2) {
               if ($('#counter').is(":visible")) {
                   $('#counter').fadeOut();
               }
           }
           else {
                             
               if (!$('#counter').is(":visible")) {
                   $('#counter').fadeIn();
               }
               
               // Set bar colours
               $('#counter').removeClass("progress-info progress-success progress-warning progress-danger progress-striped active");
               
               if (len<100) {
                   $('#counter').addClass('progress-success');
               } else if (len < 130) {
                   $('#counter').addClass('progress-warning');
               } else if (len <= 140) {
                   $('#counter').addClass('progress-danger');
               } else if (len > 140) {
                   $('#counter').addClass('progress-striped active progress-info');
               }
               
               // Set bar length
               if (len<=140) {
                   var percentage = (len/140) * 100;
                   $('#counter .bar').css('width', percentage + '%');
               }
               else {
                    $('#counter .bar').css('width', '100%');
               }

               $('#counter .bar').text(len);
           }


           
       });
    });
    </script>