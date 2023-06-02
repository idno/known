<?php

    $attachments = $vars['object']->getAttachments(); // TODO: Handle multiple
    $multiple = false;
    $num_pics = count($attachments);
    if ($num_pics > 1)
        $multiple = true;
    $cnt = 0;
?>

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
    <form action="<?php echo $vars['object']->getURL() ?>" method="post" enctype="multipart/form-data">

        <div class="row">

            <div class="col-md-8 col-md-offset-2 edit-pane">

                <h4>
                    <?php

                    if (empty($vars['object']->_id)) {
                        ?><?php echo \Idno\Core\Idno::site()->language()->_('New Photo'); ?><?php
                    } else {
                        ?><?php echo \Idno\Core\Idno::site()->language()->_('Edit Photo'); ?><?php
                    }

                    ?>
                </h4>
                
                <div class="photo-files <?php if ($multiple) echo "multiple-images"; ?>" data-num-pics="<?php echo $num_pics; ?>">
                    <?php for ($n = 0; $n < 10; $n++) { ?>
                        <div class="image-file" data-number="<?php echo $n; ?>" style="<?php if ($n > 0) echo 'display: none;'; ?>">
                            <?php echo $this->__([
                                'name' => 'photo[]',
                                'hide-existing' => $n > 0,
                                'hide-delete' => $n > 0
                            ])->draw('forms/input/image-file'); ?>
                        </div>
                    <?php } ?>
                </div>

                <div id="photo-details">

                    <div class="content-form">
                        <label for="title">
                            <?php echo \Idno\Core\Idno::site()->language()->_('Title'); ?></label>
                        <?php echo $this->__([
                            'name' => 'title',
                            'id' => 'title',
                            'placeholder' => \Idno\Core\Idno::site()->language()->_('Give it a title'),
                            'value' => $vars['object']->title,
                        'class' => 'form-control'])->draw('forms/input/input'); ?>
                    </div>

                    <?php echo $this->__([
                        'name' => 'body',
                        'value' => $vars['object']->body,
                        'wordcount' => false,
                        'class' => 'wysiwyg-short',
                        'height' => 100,
                        'placeholder' => \Idno\Core\Idno::site()->language()->_('Describe your photo'),
                        'label' => \Idno\Core\Idno::site()->language()->_('Description')
                    ])->draw('forms/input/richtext')?>

                    <?php echo $this->draw('entity/tags/input'); ?>
                    <?php echo $this->draw('content/unfurl');

            // Set focus so you can start typing straight away (on shares)
            if (\Idno\Core\Idno::site()->currentPage()->getInput('share_url')) {
                ?>
            <script>
                $(document).ready(function(){
                    var content = $('#title').val();
                    var len = content.length;
                    var element = $('#title');
                    $('#title').focus(function(){
                        $(this).prop('selectionStart', len);
                    });
                    $('#title').focus();
                });
            </script>
                <?php
            }
            ?>

            <p>
                <small><a id="inreplyto-add" href="#"
                          onclick="$('#inreplyto').append('<span><input required type=&quot;url&quot; name=&quot;inreplyto[]&quot; value=&quot;&quot; placeholder=&quot;<?php echo addslashes(\Idno\Core\Idno::site()->language()->_('Add the URL that you\'re replying to')); ?>&quot; class=&quot;form-control&quot; onchange=&quot;adjust_content(this.value)&quot; /> <small><a href=&quot;#&quot; onclick=&quot;$(this).parent().parent().remove(); return false;&quot;><icon class=&quot;fa fa-times&quot;></icon> <?php echo \Idno\Core\Idno::site()->language()->esc_('Remove URL'); ?></a></small><br /></span>'); return false;"><i class="fa fa-reply"></i>
                        <?php echo \Idno\Core\Idno::site()->language()->_('Reply to a site'); ?></a></small>
            </p>


            <div id="inreplyto">
                <?php
                if (!empty($vars['object']->inreplyto)) {
                    foreach ($vars['object']->inreplyto as $inreplyto) {
                        ?>
                            <p>
                                <input type="url" name="inreplyto[]"
                                       placeholder="Add the URL that you're replying to"
                                       class="form-control inreplyto" value="<?php echo htmlspecialchars($inreplyto) ?>" onchange="adjust_content(this.value)"/>
                                <small><a href="#"
                                          onclick="$(this).parent().parent().remove(); return false;"><i class="fa fa-times"></i>
                                      <?php echo \Idno\Core\Idno::site()->language()->_('Remove URL'); ?></a></small>
                            </p>
                        <?php
                    }
                }
                ?>
                </div>

                <?php echo $this->drawSyndication('image', $vars['object']->getPosseLinks()); ?>
                <?php if (empty($vars['object']->_id)) {
                    echo $this->__(['name' => 'forward-to', 'value' => \Idno\Core\Idno::site()->config()->getDisplayURL() . 'content/all/'])->draw('forms/input/hidden');
                } ?>
                <?php echo $this->draw('content/extra'); ?>
                <?php echo $this->draw('content/access'); ?>
                <p class="button-bar ">
                    <?php echo \Idno\Core\Idno::site()->actions()->signForm('/photo/edit') ?>
                    <input type="button" class="btn btn-cancel" value="<?php echo \Idno\Core\Idno::site()->language()->_('Cancel'); ?>" onclick="hideContentCreateForm();"/>
                    <input type="submit" class="btn btn-primary" value="<?php echo \Idno\Core\Idno::site()->language()->_('Publish'); ?>"/>
                </p>
            </div>

        </div>
    </form>
<script>
    function adjust_content(url) {
        var username = url.match(/https?:\/\/([a-z]+\.)?twitter\.com\/(#!\/)?@?([^\/]*)/)[3];
        if (username != null) {
            if ($('#title').val().search('@' + username) == -1) {
                $('#title').val('@' + username + ' ' + $('#title').val());
                count_chars();
            }
        }
    }

    $(document).ready(function () {
        $('.photo-files input').change(function(){
            var number = parseInt($(this).closest('div.image-file').attr('data-number'));
            number = number + 1;
            console.log("Showing item " + number);
            $('.photo-files .image-file[data-number='+number.toString()+']').show();
        });

        // Make in reply to a little less painful
        $("#inreplyto-add").on('dragenter', function(e) {
            var placeholder = '<?php echo addslashes(\Idno\Core\Idno::site()->language()->esc_('Add the URL that you\'re replying to')); ?>';
            e.stopPropagation();
            e.preventDefault();
            $('#inreplyto').append('<span><input required type="url" name="inreplyto[]" value="" placeholder="' + placeholder + '" class="form-control" onchange="adjust_content(this.value)" /> <small><a href="#" onclick="$(this).parent().parent().remove(); return false;"><icon class="fa fa-times"></icon> <?php echo \Idno\Core\Idno::site()->language()->esc_('Remove URL'); ?></a></small><br /></span>'); return false;
        });
    } );
</script>

<?php echo $this->draw('entity/edit/footer');
