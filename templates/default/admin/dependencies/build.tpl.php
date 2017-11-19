<?php 
$details = \Idno\Core\Version::build(). " installed";
if (\Idno\Core\Version::build() < $vars['version']) {
    $label = 'label-danger';
    $details = $vars['version'] . " - ($details)";
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= $details ?></span>
