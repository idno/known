<?php 
$details = phpversion() . " installed";
if (version_compare(phpversion(), $vars['version']) < 0) {
    $label = 'label-danger';
    $details = $vars['version'] . " - ($details)";
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= $details; ?></span> 
