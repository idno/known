<?php

if (!empty($vars['file'])) {

    $fileplot = 'window.parent.postMessage({mceAction: "customAction", data: {url: "'.\Idno\Core\Idno::site()->config()->getStaticURL() . 'file/' . $vars['file']->file['_id'].'"}}, "*");';

} else {

    $fileplot = '';

}

?>
<script>
    <?php echo $fileplot?>
    window.parent.postMessage({mceAction: 'close'}, "*");
</script>
