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
        $placeholder = \Idno\Core\Idno::site()->language()->_('Share something brilliant...');
    }
    if (!empty($vars['value'])) {
        $value = $this->autop($vars['value']);
    } else {
        $value = '';
    }

?>

<?php

    if (!empty($vars['label'])) {

        ?>
        <label for="<?=$unique_id?>"><?=htmlspecialchars($vars['label'])?></label>
        <?php

    }

?>
<div class="richtext-container">
    <!--<br class="clearall">-->
    <textarea name="<?=$vars['name']?>"  placeholder="<?=htmlspecialchars($placeholder);?>" style="height:<?=$height?>px"
          class="bodyInput mentionable wysiwyg form-control <?=$class?> <?php if (!empty($vars['required'])) echo 'validation-required'; ?>" id="<?=$unique_id?>"><?= (htmlspecialchars($value)) ?></textarea>

    <?php
    if (!empty($vars['required'])) { ?>
        <div class="required-text alert alert-danger" style="display:none;">Please complete this field.</div>
    <?php } ?>
        <br />
</div>    

<script>

    $(document).ready(function () {
        makeRichText('#<?=$unique_id?>');
    });

    function makeRichText(container) {
        $(container).tinymce({
            selector: 'textarea',
            theme: 'modern',
            skin: 'light',
            statusbar: false,
            <?php if (!empty($vars['wordcount'])) { 
                ?>statusbar: true, <?php 
            } else { 
                ?>statusbar: false,<?php } ?>
            branding: false,
            menubar: false,
            height: <?=$height?>,
            toolbar: 'styleselect | bold italic | link image | blockquote bullist numlist | alignleft aligncenter alignright | code',
            plugins: 'code link image autoresize <?php if (!empty($vars['wordcount'])) { echo " wordcount"; } ?>',
            relative_urls : false,
            remove_script_host : false,
            convert_urls : true,
            valid_children : "+body[style]",
            invalid_elements: 'div,section',
            valid_styles : 'font-style,color,text-align,text-decoration,float,display,margin-left,margin-right',
            file_picker_callback: function (callback, value, meta) {
                filePickerDialog(callback, value, meta);
            },
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
<?php

// Expose this control to the api
$this->documentFormControl($name, [
    'type' => 'richtext',
    'id' => $unique_id,
    'required' => !empty($vars['required']),
    'description' => $placeholder
]);

// Prevent bonita leakage
foreach (['unique_id', 'class', 'height', 'placeholder', 'value', 'required', 'wordcount', 'name', 'value', 'required'] as $var)
    unset($this->vars[$var]);
?>