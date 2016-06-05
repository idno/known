<?php

    if (!empty($vars['unique_id'])) {
	    $unique_id = $vars['unique_id'];
    } else {
	    $unique_id = 'body' . rand(0,9999);
    }
    if (!empty($vars['class'])) {
        $class = $vars['class'];
    } else {
        $class = '';
    }
    if (!empty($vars['height'])) {
        $height = $vars['height'];
    } else {
        $height = 500;
    }
    if (!empty($vars['placeholder'])) {
        $placeholder = $vars['placeholder'];
    } else {
        $placeholder = 'Tell your story';
    }
    if (!empty($vars['value'])) {
        $value = $this->autop($vars['value']);
    } else {
        $value = '';
    }

?>
<p style="float: right">
    <small>
        <a href="#" onclick="tinymce.EditorManager.execCommand('mceRemoveEditor',true, '<?= $unique_id; ?>'); $('#plainTextSwitch').hide(); $('#richTextSwitch').show(); return false;" id="plainTextSwitch">Switch to plain text editor</a>
        <a href="#" onclick="makeRichText('#<?=$unique_id?>'); $('#plainTextSwitch').show(); $('#richTextSwitch').hide(); return false;" id="richTextSwitch" style="display:none">Switch to rich text editor</a></small></p>
<?php

    if (!empty($vars['label'])) {

        ?>
        <label for="<?=$unique_id?>"><?=htmlspecialchars($vars['label'])?></label>
        <?php

    }

?>
    <br class="clearall">
    <textarea name="<?=$vars['name']?>"  placeholder="<?=htmlspecialchars($placeholder);?>" style="height:<?=$height?>px"
          class="bodyInput mentionable wysiwyg form-control <?=$class?>" id="<?=$unique_id?>"><?= (htmlspecialchars($value)) ?></textarea>

<?php

    if (!empty($vars['wordcount'])) {

?>
        <div class="wordcount" id="result">
            Total words <strong><span id="totalWords<?=$unique_id?>">0</span></strong>
        </div>
<?php

    } else {

    ?>
    <br>
    <?php

    }

?>

<script>

    counter = function () {

        var value = $('#<?=$unique_id?>').text();
        if (value.length == 0) {
            $('#totalWords').html(0);
            $('#totalChars').html(0);
            $('#charCount').html(0);
            $('#charCountNoSpace').html(0);
            return;
        }

        var regex = /\S+/g;
        var wordCount = knownStripHTML(value.trim()).split(' ').length; //value.trim().replace(regex, ' ').split(' ').length;
        var totalChars = value.length;
        var charCount = value.trim().length;
        var charCountNoSpace = value.replace(regex, '').length;

        $('#totalWords<?=$unique_id?>').html(wordCount);
        $('#totalChars<?=$unique_id?>').html(totalChars);
        $('#charCount<?=$unique_id?>').html(charCount);
        $('#charCountNoSpace<?=$unique_id?>').html(charCountNoSpace);

    };

    $(document).ready(function () {
        $('#<?=$unique_id?>').change(counter);
        $('#<?=$unique_id?>').keydown(counter);
        $('#<?=$unique_id?>').keypress(counter);
        $('#<?=$unique_id?>').keyup(counter);
        $('#<?=$unique_id?>').blur(counter);
        $('#<?=$unique_id?>').focus(counter);
        counter();
    });

    $(document).ready(function () {
        makeRichText('#<?=$unique_id?>');
    });

    function makeRichText(container) {
        $(container).tinymce({
            selector: 'textarea',
            theme: 'modern',
            skin: 'light',
            statusbar: false,
            menubar: false,
            height: <?=$height?>,
            toolbar: 'styleselect | bold italic | link image | blockquote bullist numlist | alignleft aligncenter alignright | code',
            plugins: 'code link image autoresize',
            relative_urls : false,
            remove_script_host : false,
            convert_urls : true,
            valid_children : "+body[style]",
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
            //url: '<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>filepicker/?type=' + meta.filetype,
            url: '<?=\Idno\Core\Idno::site()->config()->getDisplayURL()?>filepicker/?type=image',
            width: 650,
            height: 550
        }, {
            oninsert: function (url) {
                callback(url);
            }
        });
    }
</script>