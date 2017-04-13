<?php 
$details = \Idno\Core\Idno::site()->getMachineVersion(). " installed";
if (\Idno\Core\Idno::site()->getMachineVersion() < $vars['version']) {
    $label = 'label-danger';
    $details = $vars['version'] . " - ($details)";
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= $details ?></span>
