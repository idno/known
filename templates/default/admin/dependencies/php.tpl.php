<?php 
if (strnatcmp(phpversion(), $vars['version']) <= 0) {
    $label = 'label-important';
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= phpversion() ?> installed</span> 
