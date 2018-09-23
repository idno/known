<?php
$details = \Idno\Core\Idno::site()->language()->_('%s installed', [\Idno\Core\Version::version()]);
if (version_compare(\Idno\Core\Version::version(), strtolower($vars['version'])) < 0) {
    $label = 'label-danger';
    $details = $vars['version'] . " - ($details)";
} else {
    $label = 'label-success';
}    ?><span class="label <?php echo $label ?>"><?php echo $details; ?></span>
