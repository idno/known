<?php

    if (!empty($vars['file'])) {

        $fileplot = 'top.tinymce.activeEditor.windowManager.getParams().oninsert("'.\Idno\Core\site()->config()->getDisplayURL() . 'file/' . $vars['file']->file['_id'].'");';

    } else {

        $fileplot = '';

    }

?>
<script>
    <?=$fileplot?>
    top.tinymce.activeEditor.windowManager.close();
</script>