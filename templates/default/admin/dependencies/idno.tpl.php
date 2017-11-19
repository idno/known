<?php 
$details = \Idno\Core\Version::version(). " installed";
if (version_compare(\Idno\Core\Version::version(), strtolower($vars['version'])) < 0) {
    $label = 'label-danger';
    $details = $vars['version'] . " - ($details)";
} else {
    $label = 'label-success';
}    ?><span class="label <?= $label ?>"><?= $details; ?></span>
