<?php

    if (!empty($vars['file'])) {

        $fileplot = 'parent.tinymce.activeEditor.windowManager.getParams().oninsert("'.\Idno\Core\Idno::site()->config()->getDisplayURL() . 'file/' . $vars['file']->file['_id'].'");';

    } else {

        $fileplot = '';

    }

?>
<script>
    <?=$fileplot?>
    parent.tinymce.activeEditor.windowManager.close();
</script>