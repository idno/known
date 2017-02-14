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
<!--<p style="float: right">
    <small>
        <a href="#" onclick="tinymce.EditorManager.execCommand('mceRemoveEditor',true, '<?= $unique_id; ?>'); $('#plainTextSwitch').hide(); $('#richTextSwitch').show(); return false;" id="plainTextSwitch">Switch to plain text editor</a>
        <a href="#" onclick="makeRichText('#<?=$unique_id?>'); $('#plainTextSwitch').show(); $('#richTextSwitch').hide(); return false;" id="richTextSwitch" style="display:none">Switch to rich text editor</a></small></p>-->
<?php

    if (!empty($vars['label'])) {

        ?>
        <label for="<?=$unique_id?>"><?=htmlspecialchars($vars['label'])?></label>
        <?php

    }

?>
    <!--<br class="clearall">-->
    <textarea id="<?= $unique_id ?>" name="<?=$vars['name']?>"  placeholder="<?=htmlspecialchars($placeholder);?>" style="display: none;"
          class="bodyInput mentionable wysiwyg form-control <?=$class?>" id="<?=$unique_id?>"><?= (htmlspecialchars($value)) ?></textarea>
          
          <div id="<?= $unique_id ?>_editor" style="height:<?=$height?>px"></div>

<?php

    if (!empty($vars['wordcount'])) {

?>
        <div class="wordcount" id="result">
            Total words <strong><span id="totalWords<?=$unique_id?>">0</span></strong>
        </div>
<?php

    } else {

    ?>
    <!--<br>-->
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
        var wordCount = knownStripHTML(value).replace(/\n/g,' ').trim().split(' ').length;
        var totalChars = value.length;
        var charCount = value.trim().length;
        var charCountNoSpace = value.replace(regex, '').length;

        $('#totalWords<?=$unique_id?>').html(wordCount);
        $('#totalChars<?=$unique_id?>').html(totalChars);
        $('#charCount<?=$unique_id?>').html(charCount);
        $('#charCountNoSpace<?=$unique_id?>').html(charCountNoSpace);

    };
       
    $(document).ready(function() {
        /* Build quill */
        var quill = new Quill('#<?= $unique_id; ?>_editor', {
          modules: { toolbar: [
            [{ header: [1, 2, false] }],
            ['bold', 'italic', 'underline', 'blockquote'],
            [{list: 'ordered'}, {list: 'bullet'}],
            ['image', 'video', 'link']
            ] },
          placeholder: "<?=htmlspecialchars($placeholder);?>",
          theme: 'snow'
        });
        
        /* Initialise with content */
        $('#<?= $unique_id ?>_editor div.ql-editor').html($('#<?= $unique_id; ?>').text());
        
        /* Handle text change */
        quill.on('text-change', function() {
          $('#<?= $unique_id ?>').text($('#<?= $unique_id; ?>_editor div.ql-editor').html()); // This is a horrible hack.
          
          counter();
        });
    });
</script>
