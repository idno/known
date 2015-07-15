<?php 
if (version_compare(phpversion(), $vars['version']) < 0) {
    $label = 'label-danger';
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= phpversion() ?> installed</span> 
